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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\App;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class Emitter
 * @package Gram\App
 *
 * Erstellt die Ausgabe für den Browser von dem Response
 */
class Emitter
{
	public function emit(ResponseInterface $response)
	{
		//Sende Header

		if (headers_sent()) {
			throw new RuntimeException('Headers were already sent. The response could not be emitted!');
		}

		//Erstelle den Status Header
		$statusLine = sprintf('HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		);

		header($statusLine, true);

		//Sende weitere Header die noch hinzugefügt wurden
		foreach ($response->getHeaders() as $name=>$header) {
			$responseHeader = sprintf('%s: %s',
				$name,
				$response->getHeaderLine($name)
			);
			header($responseHeader, false);
		}

		//Sende Body
		echo $response->getBody()->__toString();
		exit();	//schließe die Seite, keine weitere Ausgabe mehr möglich
	}
}