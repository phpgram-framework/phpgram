<?php
namespace Gram\Route\Generator;

/**
 * Class DynamicGenerator
 * @package Gram\Route\Generator
 * @author Jörn Heinemann
 * Erstellt Routelisten mit Group Count Based (GCB)
 * Wird von den Collector Klassen aufgerufen um die Routes und Handler zusammen zufassen
 * Anlehnung an: http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 */
class DynamicGenerator
{
	private $number=0,$chunkcount=0,$maxChunkSize,$routeCollector,$handleCollector,$routeList,$handlerList,$route;

	public function __construct($maxChunk){
		$this->maxChunkSize=$maxChunk;
	}

	/**
	 * Sammle solange Routes bis die Sammelmenge (chunk) erreicht ist (routeCollector())
	 * Wenn diese erreicht ist fasse die Routes zu einer Regex zusammen (chunkRoutes())
	 * @param array $routes
	 * Die zu chunkenden Routes. Array muss die Route, den Handler und die Anzahl an erwarteten Vartaiblen beinhalten und
	 * wie folgt aufgebaut sein:
	 * $routes[1-n]=array("route"=>$route,"handle"=>$handle,"vars"=>$varcount);
	 * @return array
	 * Gebe Route und Handlerliste zurück
	 */
	public function generateChunk(array $routes){
		foreach ($routes as $this->route) {
			//sammle solange Routes zum gruppieren bis chunk erreicht ist
			if($this->chunkcount<$this->maxChunkSize-1){
				$this->routeCollector();
				++$this->chunkcount;
				continue;
			}

			$this->routeCollector();	//letzte Route für die liste noch hinzufügen

			$this->chunkRoutes();	//routes chunken
		}

		if(!empty($this->routeCollector)){
			$this->chunkRoutes();	//letze routes chunken
		}

		return array(
			'regexes'=>$this->routeList,
			'dynamichandler'=>$this->handlerList
		);
	}

	/**
	 * Fasse die Routes zusammen.
	 * Muster: (?| Routes mit | dazwischen )
	 * Speichere Handler an die gleiche Nummer wie die Routeliste
	 */
	private function chunkRoutes(){
		$this->routeList[] = '~^(?|' . implode('|', $this->routeCollector) . ')$~x'; //wandle die Routes in ein gemeinsames Regex um
		$this->handlerList[]=$this->handleCollector;	//übergibt die handler für die Routeliste

		//Sammler wieder zurück stellen
		$this->routeCollector=array();
		$this->handleCollector=array();
		$this->chunkcount=0;
		$this->number=0;	//Für die Placeholder
	}

	/**
	 * Fügt Routeing Placeholder hinzu: ()
	 * Wenn die Route bereits Variablen hat-> von den Platzhaltern abziehen, da diese bereits Placeholder sind
	 * Placeholder sind notwendig da die Routes zu einer regex zusammengefasst werden und das matcharray die gleiche Länge haben muss
	 * wie die Position der richtigen Route in der Regex. Erst dann kann der richtige Handler geladen werden (der die gleiche Position in seinem Array hat)
	 * bsp.: Route /video/{id} steht an 3. Stelle in der Regex -> /video/{id}()
	 * Route /video/add steht an 2. Stelle -> /video/add()
	 * Route /video/added steht an 4. Stelle -> /video/added()()()
	 * -> Placeholder werden aufgefüllt
	 */
	private function routeCollector(){
		$this->number=max($this->number,$this->route['vars']);	//passe Placeholderanzahl an
		$this->routeCollector[]=$this->route['route'].str_repeat('()', $this->number - $this->route['vars']);	//gruppiere die routes, füge placeholder hinzu abzgl. der Varialben
		++$this->number;	//erhöhe da die nächste Route einen Playerholder mehr braucht
		$this->handleCollector[$this->number]=$this->route['handle'];	//gruppiere die Handler an der gleichen Stelle wie die Regex, hier number +1 da der Match mindestens bei 1 anfängt
	}
}