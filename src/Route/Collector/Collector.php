<?php
namespace Gram\Route\Collector;
use Gram\Route\Generator\DynamicGenerator;
use Gram\Route\Generator\StaticGenerator;
use Gram\Route\Handler\CallbackHandler;
use Gram\Route\Handler\ClassHandler;

abstract class Collector
{
	protected static $placeholders=array(
		'/\/{(a)}/'=>'/(\w*)',	//Alphanumerisch
		'/\/{(id)}/'=>'/(\d+)'	//Nur Zahlen
	),$userplaceholders=array();
	protected $map=array(),$dynamicroutes=array(),$staticroutes=array();

	/**
	 * Gibt alle verfügbaren Routes zurück
	 * @return array
	 */
	public function map(){
		return $this->map;
	}

	/**
	 * Sammelt erst alle Routes die geadded werden
	 * Wandelt die Userplaceholder in Regex um und zählt wie viele Varaiblen die Route parsen soll
	 * @param string $route
	 * Die Route von den Configseiten
	 * @param $handle
	 * Was bei einem Fund gemacht werden soll
	 */
	protected function set($route,$handle){
		//wandle Platzhalter um
		$allPlaceHolders=array_merge(self::$placeholders,self::$userplaceholders);

		$varcount=0;
		foreach ($allPlaceHolders as $pattern=>$placeHolder) {
			$route = preg_replace($pattern, $placeHolder, $route,-1,$countvar);
			$varcount+=$countvar; //zähle die Varaiblen die die Funktion erwartet (für Placeholder: () )
		}

		$route = preg_replace('/\/{(.*?)}/', '/(.*?)', $route,-1,$countvar);	//Alles
		$varcount+=$countvar;


		if($varcount===0){
			//Eine Staticroute
			$this->staticroutes[]=array(
				"route"=>$route,
				"handle"=>$handle
			);
		}else{
			//Eine Route mit Parametern
			$this->dynamicroutes[]=array(
				"route"=>$route,
				"handle"=>$handle,
				"vars"=>$varcount
			);
		}
	}

	/**
	 * Fasst die gesammelten Routes mit den Generatoren zusammen
	 */
	protected function trigger(){
		$staticgenerator = new StaticGenerator();
		$dynamicgenerator=new DynamicGenerator(REGEXCHUNK);
		$this->map=array_merge(
			$this->map,
			$staticgenerator->generate($this->staticroutes),
			$dynamicgenerator->generateChunk($this->dynamicroutes)
		);
	}

	protected function createCallbackForMVC($controller,$function){
		$callback = new ClassHandler();
		try{
			$callback->set($controller,$function);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}

	protected function createCallbackFromCallable(callable $callable){
		$callback= new CallbackHandler();
		try{
			$callback->set($callable);
		}catch (\Exception $e){
			echoExep($e);
			return false;
		}

		return $callback;
	}
}