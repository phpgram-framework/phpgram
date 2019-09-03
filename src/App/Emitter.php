<?php
namespace Gram\App;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Emitter
{
	public function emit(ResponseInterface $response)
	{
		//Sende Header

		if (headers_sent()) {
			throw new RuntimeException('Headers were already sent. The response could not be emitted!');
		}

		$statusLine = sprintf('HTTP/%s %s %s',
			$response->getProtocolVersion(),
			$response->getStatusCode(),
			$response->getReasonPhrase()
		);

		header($statusLine, true);

		foreach ($response->getHeaders() as $name=>$header) {
			$responseHeader = sprintf('%s: %s',
				$name,
				$response->getHeaderLine($name)
			);
			header($responseHeader, false);
		}

		//Sende Body
		echo $response->getBody()->__toString();
	}
}