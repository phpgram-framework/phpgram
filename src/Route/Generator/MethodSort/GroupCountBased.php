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

use Gram\Route\Generator\DynamicGeneratorTrait;

/**
 * Class GroupCountBased
 * @package Gram\Route\Generator\MethodSort
 *
 * Ein Generator der die Routes ihrer Method zuordnet
 *
 * Based on:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class GroupCountBased extends StaticGenerator
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
		foreach ($routes as $method=>$route) {
			$chunkSize=$this->getChunkSize(\count($route),self::CHUNKSIZE);	//passe die chunk größe an

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
}