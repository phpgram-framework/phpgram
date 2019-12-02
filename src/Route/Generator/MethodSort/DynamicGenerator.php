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

namespace Gram\Route\Generator\MethodSort;

use Gram\Route\Route;

class DynamicGenerator extends Generator
{
	const CHUNKSIZE = 10;

	private $number=0,$chunkcount=0,$routeCollector,$handleCollector,$routeList,$handlerList;

	/**
	 * Sammle solange Routes bis die Sammelmenge (chunk) erreicht ist ( @see DynamicGenerator::routeCollector() )
	 *
	 * Wenn diese erreicht ist fasse die Routes zu einer Regex zusammen ( @see DynamicGenerator::chunkRoutes() )
	 *
	 * @param array $routes
	 *
	 * Die zu chunkenden Routes. Array muss die Route, den Handler und die Anzahl an erwarteten Vartaiblen beinhalten und
	 * wie folgt aufgebaut sein:
	 *
	 * $routes[1-n]=array("route"=>$route,"handle"=>$handle,"vars"=>$varcount);
	 *
	 * @return array
	 * Gebe Route und Handlerliste zurück
	 */
	public function generateDynamic(array $routes)
	{
		foreach ($routes as $method=>$route) {
			$chunkSize=$this->getChunkSize(\count($route));	//passe die chunk größe an

			foreach ($route as $routeitem) {
				//sammle solange Routes zum gruppieren bis chunk erreicht ist
				if($this->chunkcount<$chunkSize-1){
					$this->routeCollector($routeitem);
					++$this->chunkcount;
					continue;
				}

				$this->routeCollector($routeitem);	//letzte Route für die liste noch hinzufügen

				$this->chunkRoutes($method);	//routes chunken
			}

			if(!empty($this->routeCollector)){
				$this->chunkRoutes($method);	//letze routes chunken
			}
		}

		return [
			'regexes'=>$this->routeList,
			'dynamichandler'=>$this->handlerList
		];
	}

	private function reset()
	{
		//Sammler wieder zurück stellen
		$this->routeCollector=[];
		$this->handleCollector=[];
		$this->chunkcount=0;
		$this->number=0;	//Für die Placeholder
	}

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
	 *
	 * Wie viele Routes gechunkt werden sollen
	 *
	 * @return int
	 */
	private function getChunkSize($count)
	{
		$approxChunks = \max(1, \round($count/self::CHUNKSIZE));	//wie viele Chunks lassen sich erstellen (muss min. einen geben)

		return (int) \ceil($count / $approxChunks);		//die tatsächliche Größe, um die Anzahl an Chunks zu minimieren
	}

	/**
	 * Fasse die Routes zusammen.
	 *
	 * Muster: (?| Routes mit | dazwischen )
	 *
	 * Speichere Handler an die gleiche Nummer wie die Routeliste
	 * @param $method
	 */
	private function chunkRoutes($method)
	{
		$this->routeList[$method][] = '~^(?|' . \implode('|', $this->routeCollector) . ')$~x'; //wandle die Routes in ein gemeinsames Regex um
		$this->handlerList[$method][] = $this->handleCollector;	//übergibt die handler für die Routeliste

		$this->reset();
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
	private function routeCollector(Route $route)
	{
		$varcount = \count($route->vars);	//zähle die Varaiblen die die Funktion erwartet (für Placeholder: () )
		$this->number = \max($this->number,$varcount);	//passe Placeholderanzahl an
		$this->routeCollector[]= $route->path. \str_repeat('()', $this->number - $varcount);	//gruppiere die routes, füge placeholder hinzu abzgl. der Varialben
		++$this->number;	//erhöhe da die nächste Route einen Playerholder mehr braucht
		$this->handleCollector[$this->number]=[$route->handle,$route->vars];	//gruppiere die Handler an der gleichen Stelle wie die Regex, hier number +1 da der Match mindestens bei 1 anfängt
	}

}