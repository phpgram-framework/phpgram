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

/**
 * Class DynamicGenerator
 * @package Gram\Route\Generator
 *
 * Erstellt Routelisten mit Group Count Based (GCB)
 *
 * Wird von den Collector Klassen aufgerufen um die Routes und Handler zusammen zufassen
 *
 * Based on:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class DynamicGenerator extends Generator
{
	use DynamicGeneratorTrait;

	const CHUNKSIZE = 10;

	/**
	 * @inheritdoc
	 *
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
		$chunkSize=$this->getChunkSize(\count($routes),self::CHUNKSIZE);	//passe die chunk größe an

		foreach ($routes as $route) {
			//sammle solange Routes zum gruppieren bis chunk erreicht ist
			if($this->chunkcount<$chunkSize-1){
				$this->routeCollector($route);
				++$this->chunkcount;
				continue;
			}

			$this->routeCollector($route);	//letzte Route für die liste noch hinzufügen

			$this->chunkRoutes();	//routes chunken
		}

		if(!empty($this->routeCollector)){
			$this->chunkRoutes();	//letze routes chunken
		}

		return [
			'regexes'=>$this->routeList,
			'dynamichandler'=>$this->handlerList
		];
	}

	/**
	 * Fasse die Routes zusammen.
	 *
	 * Muster: (?| Routes mit | dazwischen )
	 *
	 * Speichere Handler an die gleiche Nummer wie die Routeliste
	 */
	private function chunkRoutes()
	{
		$this->routeList[] = '~^(?|' . \implode('|', $this->routeCollector) . ')$~x'; //wandle die Routes in ein gemeinsames Regex um
		$this->handlerList[] = $this->handleCollector;	//übergibt die handler für die Routeliste

		$this->reset();
	}
}