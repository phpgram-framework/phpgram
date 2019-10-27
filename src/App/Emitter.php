<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\App;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class Emitter
 * @package Gram\App
 *
 * Based on:
 * @see https://github.com/zendframework/zend-httphandlerrunner
 * @see https://github.com/zendframework/zend-httphandlerrunner/blob/master/LICENSE.md
 *
 * Erstellt die Ausgabe für den Browser von dem Response
 */
class Emitter
{
	private $maxBufferLength;

	public function __construct(int $maxBufferLength=4096)
	{
		$this->maxBufferLength=$maxBufferLength;
	}

	public function emit(ResponseInterface $response)
	{
		if (headers_sent()) {
			throw new RuntimeException('Headers were already sent. The response could not be emitted!');
		}

		$this->emitStatusLine($response);
		$this->emitHeader($response);

		$range = $this->parseContentRange($response->getHeaderLine('Content-Range'));

		if (null === $range || 'bytes' !== $range[0]) {
			$this->emitBody($response);
		}else{
			$this->emitBodyRange($range, $response);
		}

		exit();	//schließe die Seite, keine weitere Ausgabe mehr möglich
	}

	private function emitStatusLine(ResponseInterface $response)
	{
		$status = $response->getStatusCode();

		//Erstelle den Status Header
		$statusLine = sprintf('HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$status,
			$response->getReasonPhrase()
		);

		header($statusLine, true,$status);
	}

	private function emitHeader(ResponseInterface $response)
	{
		$status=$response->getStatusCode();

		//Sende weitere Header die noch hinzugefügt wurden
		foreach ($response->getHeaders() as $headers=>$values) {
			$name = ucwords($headers,'-');
			$first = $name === 'Set-Cookie' ? false : true;

			//wenn header ein Array sende alle werte im Array
			foreach ($values as $value) {
				$responseHeader = sprintf('%s: %s',
					$name,
					$value
				);

				header($responseHeader, $first,$status);
				$first=false;
			}
		}
	}

	private function emitBody(ResponseInterface $response)
	{
		$body = $response->getBody();

		if($body->isSeekable()){
			$body->rewind();
		}

		if(!$body->isReadable()){
			echo $body;
			return;
		}

		while(!$body->eof()){
			echo $body->read($this->maxBufferLength);
		}
	}

	/**
	 * @copyright Zend Technologies USA, Inc. All rights reserved.
	 * @see https://github.com/zendframework/zend-httphandlerrunner/blob/master/LICENSE.md
	 *
	 * @param array $range
	 * @param ResponseInterface $response
	 */
	private function emitBodyRange(array $range,ResponseInterface $response)
	{
		[$unit, $first, $last, $length] = $range;

		$body = $response->getBody();

		if ($body->isSeekable()) {
			$body->seek($first);
			$first = 0;
		}

		$length = $last - $first + 1;

		if (!$body->isReadable()){
			echo substr($body->getContents(), $first, $length);
			return;
		}

		$remaining = $length;

		while ($remaining >= $this->maxBufferLength && ! $body->eof()) {
			$contents   = $body->read($this->maxBufferLength);
			$remaining -= strlen($contents);
			echo $contents;
		}
		if ($remaining > 0 && ! $body->eof()) {
			echo $body->read($remaining);
		}
	}

	/**
	 * @copyright Zend Technologies USA, Inc. All rights reserved.
	 * @see https://github.com/zendframework/zend-httphandlerrunner/blob/master/LICENSE.md
	 *
	 * Parse content-range header
	 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
	 *
	 * @param string $header
	 * @return null|array [unit, first, last, length]; returns null if no
	 *     content range or an invalid content range is provided
	 */
	private function parseContentRange(string $header) : ?array
	{
		if (! preg_match('/(?P<unit>[\w]+)\s+(?P<first>\d+)-(?P<last>\d+)\/(?P<length>\d+|\*)/', $header, $matches)) {
			return null;
		}
		return [
			$matches['unit'],
			(int) $matches['first'],
			(int) $matches['last'],
			$matches['length'] === '*' ? '*' : (int) $matches['length'],
		];
	}
}