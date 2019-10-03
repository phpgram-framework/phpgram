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
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface HandlerInterface
 * @package Gram\Middleware\Handler
 *
 * Ein Interface für Handler die von Middleware ausgeführt werden können
 */
interface HandlerInterface
{
	public function handle(ServerRequestInterface $request);
}