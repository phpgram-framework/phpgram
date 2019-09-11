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

namespace Gram\Middleware;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Controller
 * @package Gram\Middleware
 *
 * Wenn Controller hiervon erben erhalten diese Zugriff auf des Request und können ihn verändern
 */
abstract class Controller
{
	/** @var ServerRequestInterface */
	protected $request;

	public function setPsr(ServerRequestInterface $request)
	{
		$this->request=$request;
	}
}