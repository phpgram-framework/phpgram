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

namespace Gram\Middleware\Handler;

use Gram\Exceptions\PageNotAllowedException;
use Gram\Exceptions\PageNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class NotFoundHandler
 * @package Gram\Middleware\Handler
 *
 * Ein Handler der von der Routing Middleware aufgerufen wird, sollte 404 oder 405 eintreten
 */
class NotFoundHandler implements RequestHandlerInterface
{
	private $callbackHandler;

	public function __construct(RequestHandlerInterface $callbackHandler)
	{
		$this->callbackHandler=$callbackHandler;
	}

	/**
	 * @inheritdoc
	 * @throws \Exception
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		$callable = $request->getAttribute('callable',null);

		if ($callable!==null){
			return $this->callbackHandler->handle($request);
		}

		$status = $request->getAttribute('status',404);

		if($status==405){
			throw new PageNotAllowedException('405 Method not allowed');
		}

		throw new PageNotFoundException('404 Page Not Found');
	}
}