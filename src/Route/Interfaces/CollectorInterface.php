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

	public function get404();
	public function get405();
	public function set404($handle);
	public function set405($handle);
	public function get(string $route,$handler);
	public function post(string $route,$handler);
	public function getpost(string $route,$handler);
	public function delete(string $route,$handler);
	public function put(string $route,$handler);
	public function patch(string $route,$handler);
	public function setBase(string $base);
	public function getBase();
}