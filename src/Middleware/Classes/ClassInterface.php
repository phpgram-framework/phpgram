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

namespace Gram\Middleware\Classes;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ControllerInterface
 * @package Gram\Middleware\Controller
 *
 * Interface für alle Classes, das Psr setzt und die möglicherweise veränderten Psr zurück gibt
 */
interface ClassInterface
{
	/**
	 * Setze Psr Object(s) in der Klasse
	 *
	 * @param ServerRequestInterface $request
	 * @return mixed
	 */
	public function setPsr(ServerRequestInterface $request);

	/**
	 * Gebe Psr Object(s) wieder zurück
	 *
	 * @return ServerRequestInterface
	 */
	public function getRequest():ServerRequestInterface;
}