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

namespace Gram\Middleware\Classes;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
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
	 * @param ResponseInterface $response
	 * @return mixed
	 */
	public function setPsr(ServerRequestInterface $request, ResponseInterface $response);

	/**
	 * Gebe den Response wieder zurück
	 *
	 * @return ResponseInterface
	 */
	public function getResponse(): ResponseInterface;

	/**
	 * Setzt den DI Container sodass auch Klassen darauf zugriff haben
	 *
	 * @param ContainerInterface $container
	 * @return mixed
	 */
	public function setContainer(ContainerInterface $container = null);

	/**
	 * Gibt einen Wert aus dem DI Container zurück
	 * mit $this->value
	 *
	 * Wirft eine Exception wenn es den Wert nicht gibt
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __get($name);
}