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
 * Trait DynamicGeneratorTrait
 * @package Gram\Route\Generator
 *
 * Allgemeine Methods für die Generators
 */
trait DynamicGeneratorTrait
{
	protected $routeList,$handlerList;

	/**
	 * Gibt die jeweilige angepeilte chunksize zurück
	 *
	 * @return int
	 */
	abstract protected function getChunkSize():int;

	/**
	 * Erstellt Regex für den jeweiligen Chunk
	 *
	 * @param array $chunk
	 * @param $method
	 * @return mixed
	 */
	abstract protected function chunkRoutes(array &$chunk,$method);

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
	public function generateDynamic(array &$routes):array
	{
		foreach ($routes as $method=>$route) {
			$chunkSize=$this->generateChunkSize(\count($route),$this->getChunkSize());	//passe die chunk größe an

			$chunks = \array_chunk($route,$chunkSize,true);

			//schleife, da diese schneller als array_map() ist
			foreach ($chunks as $chunk) {
				$this->chunkRoutes($chunk,$method);
			}
		}

		return [
			'regexes'=>$this->routeList,
			'dynamichandler'=>$this->handlerList
		];
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
	 * Wie viele Routes gechunkt werden sollen
	 *
	 * @param $chunk_size
	 * die normale Chunk Größe
	 *
	 * @return int
	 */
	protected function generateChunkSize($count,$chunk_size)
	{
		$approxChunks = \max(1, \round($count/$chunk_size));	//wie viele Chunks lassen sich erstellen (muss min. einen geben)

		return (int) \ceil($count / $approxChunks);		//die tatsächliche Größe, um die Anzahl an Chunks zu minimieren
	}
}