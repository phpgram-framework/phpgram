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

namespace Gram\Route\Interfaces;

use Gram\Route\Route;
use Gram\Route\RouteGroup;

/**
 * Interface CollectorInterface
 * @package Gram\Route\Interfaces
 *
 * Interface für Collector Klassen
 */
interface CollectorInterface
{
	/**
	 * Fügt eine Route hinzu
	 *
	 * Gibt ein Routeobjekt zurück um Route Middleware und Strategy fest zu legen
	 *
	 * @param string $path
	 * @param $handler
	 * @param array $method
	 * @return Route
	 */
	public function add(string $path,$handler,array $method):Route;

	/**
	 * Startet eine Gruppe
	 *
	 * Zuerst werden alle alten Werte (prefix, Gruppenid) gesichert
	 *
	 * Dann werden diese angepasst und die Gruppen Funktion wird gestartet
	 *
	 * Nach der Funktion werden die alten Werte wieder hergestellt
	 *
	 * Nested Groups sind ebenfalls möglich
	 *
	 * Gibt Routegroup zurück um Group Middleware und Strategy hinzu zufügen
	 *
	 * @param $prefix
	 * @param callable $groupcollector
	 * @return RouteGroup
	 */
	public function group($prefix,callable $groupcollector):RouteGroup;

	/**
	 * Gibt dem Dispatcher die benötigten Daten
	 *
	 * Püft ob es einen Cache gibt, wenn ja wird dieser geladen
	 *
	 * Wenn nicht werden die Daten erst generiert
	 *
	 * @return array
	 */
	public function getData():array;

	/**
	 * Gibt zu einer geg. Route Id die passende Route zurück
	 *
	 * @param int $routeId
	 * @return Route|null
	 */
	public function getRoute(int $routeId):Route;

	/**
	 * Gebe den 404 Handler zurück
	 *
	 * @return mixed
	 */
	public function get404();

	/**
	 * Gebe den 405 Handler zurück
	 *
	 * @return mixed
	 */
	public function get405();

	/**
	 * Setze den 404 Handler
	 *
	 * @param $handle
	 */
	public function set404($handle);

	/**
	 * Setze den 405 Handler
	 *
	 * @param $handle
	 */
	public function set405($handle);

	/**
	 * Füge eine GET Route hinzu
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function get(string $route,$handler):Route;

	/**
	 * Füge eine POST Route hinzu
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function post(string $route,$handler):Route;

	/**
	 * Füge eine Route hinzu die GET und POST sein kann
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function getpost(string $route,$handler):Route;

	/**
	 * Füge eine DELETE Route hinzu
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function delete(string $route,$handler):Route;

	/**
	 * Füge eine PUT Route hinzu
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function put(string $route,$handler):Route;

	/**
	 * Füge eine PATCH Route hinzu
	 *
	 * @param string $route
	 * @param $handler
	 * @return Route
	 */
	public function patch(string $route,$handler):Route;

	/**
	 * Setze den Base Path der vor jeder Route hinzugefügt wird
	 *
	 * @param string $base
	 * @return mixed
	 */
	public function setBase(string $base);

	/**
	 * Gebe den basepath wieder zurück
	 *
	 * @return string
	 */
	public function getBase():string;
}