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

namespace Gram\Route\Generator;

use Gram\Route\Route;

/**
 * Trait DynamicGeneratorTrait
 * @package Gram\Route\Generator
 *
 * Allgemeine Methods für die Generators
 */
trait DynamicGeneratorTrait
{
	protected $number=0,$chunkcount=0,$routeCollector,$handleCollector,$routeList,$handlerList;

	/**
	 * Gibt die tatsächliche Chunkgröße an
	 *
	 * 1. Ermittle wie viele Routes gechunkt werden sollen
	 *
	 * 2. Errechne die Anzahl an Chunks die mit der Standardgröße erstellt werden könnten. (Runde die Anzahl)
	 *
	 * 3. Damit keine kleinen Chunks über bleiben:
	 *
	 * - Errechne die neue Chunkgröße, sodass alle Chunks immer voll sind:
	 *
	 * - indem die Anzahl an Routes / mit der Anzahl an Chunks aufgerundet wird
	 *
	 * - bsp.: Anzahl = 11, Chunksize = 10
	 *
	 * -> 21/10 = 2,1 -> runden: 2 -> die Anzahl an Chunks mit einer Größe von 10
	 *
	 * -> 21/2 = 10,5 -> aufrunden: 11 -> Größe an Chunks die am Ende immer voll sind
	 *
	 * @param $count
	 * Wie viele Routes gechunkt werden sollen
	 *
	 * @param $chunk_size
	 * die normale Chunk Größe
	 *
	 * @return int
	 */
	protected function getChunkSize($count,$chunk_size)
	{
		$approxChunks = \max(1, \round($count/$chunk_size));	//wie viele Chunks lassen sich erstellen (muss min. einen geben)

		return (int) \ceil($count / $approxChunks);		//die tatsächliche Größe, um die Anzahl an Chunks zu minimieren
	}

	protected function reset()
	{
		//Sammler wieder zurück stellen
		$this->routeCollector=[];
		$this->handleCollector=[];
		$this->chunkcount=0;
		$this->number=0;	//Für die Placeholder
	}

	/**
	 * Fügt Routeing Placeholder hinzu: ()
	 *
	 * Wenn die Route bereits Variablen hat-> von den Platzhaltern abziehen, da diese bereits Placeholder sind
	 *
	 * Placeholder sind notwendig da die Routes zu einer regex zusammengefasst werden und das matcharray
	 * die gleiche Länge haben muss
	 *
	 * wie die Position der richtigen Route in der Regex. Erst dann kann der richtige Handler geladen werden
	 * (der die gleiche Position in seinem Array hat)
	 *
	 * bsp.: Route /video/{id} steht an 3. Stelle in der Regex -> /video/{id}()
	 *
	 * Route /video/add steht an 2. Stelle -> /video/add()
	 *
	 * Route /video/added steht an 4. Stelle -> /video/added()()()
	 *
	 * -> Placeholder werden aufgefüllt
	 *
	 * @param Route $route
	 */
	protected function routeCollector(Route $route)
	{
		$varcount = \count($route->vars);	//zähle die Varaiblen die die Funktion erwartet (für Placeholder: () )
		$this->number = \max($this->number,$varcount);	//passe Placeholderanzahl an
		$this->routeCollector[]= $route->path. \str_repeat('()', $this->number - $varcount);	//gruppiere die routes, füge placeholder hinzu abzgl. der Varialben
		++$this->number;	//erhöhe da die nächste Route einen Playerholder mehr braucht
		$this->handleCollector[$this->number]=[$route->routeid,$route->vars];	//gruppiere die Handler an der gleichen Stelle wie die Regex, hier number +1 da der Match mindestens bei 1 anfängt
	}
}