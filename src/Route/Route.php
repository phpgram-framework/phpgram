<?php
namespace Gram\Route;
use Gram\Route\Collector\MiddlewareCollector;

class Route
{
	public $path,$handle,$method,$varcount,$path_Old;

	private static $placeholders=array(
		'/\/{(a)}/'=>'/(\w*)',	//Alphanumerisch
		'/\/{(id)}/'=>'/(\d+)',	//Nur Zahlen
		'/\/{(auml)}/'=>'/([0-9,a-z,A-Z_äÄöÖüÜß]+)'	//Umlaute
	);
	public static $userplaceholders=array();


	public function __construct(string $path,$handle,string $method){
		$this->handle=$handle;
		$this->method=$method;

		$this->handle['method']=$method;	//speichere Method für Dispatcher
		$this->path=$path;
		$this->path_Old=$path;

		$this->parsePlaceholders();
	}

	public function addMiddleware(array $middleware,$type){
		//eine Middleware kann selber keine weiteren hinzufügen
		if($this->method===''){
			return;
		}

		//füge die Middleware nach vorne, damit diese route nicht überschrieben werden kann mit anderen middlewares
		MiddlewareCollector::middle($type)->add($this->path_Old,$middleware,true);
	}

	private function parsePlaceholders(){
		//wandle Platzhalter um
		$allPlaceHolders=array_merge(self::$placeholders,self::$userplaceholders);

		$this->varcount=0;
		foreach ($allPlaceHolders as $pattern=>$placeHolder) {
			$this->path = preg_replace($pattern, $placeHolder, $this->path,-1,$countvar);
			$this->varcount+=$countvar; //zähle die Varaiblen die die Funktion erwartet (für Placeholder: () )
		}

		//wandle alles andere um
		$this->path = preg_replace('/\{(.*?)}/', '(.*?)', $this->path,-1,$countvar);	//Alles
		$this->varcount+=$countvar;
	}
}