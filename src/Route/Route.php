<?php
namespace Gram\Route;
use Gram\Route\Collector\MiddlewareCollector;
use Gram\Route\Interfaces\Parser;

class Route
{
	public $path,$handle,$method,$vars,$path_Old;
	private static $parser;

	public function __construct(string $path,$handle,$method){
		$this->handle=$handle;
		$this->method=$method;

		$this->handle['method']=$method;	//speichere Method für Dispatcher
		$this->path=$path;
		$this->path_Old=$path;

		$this->createRoute();
	}

	private function parseRoute(Parser $parser){
		return $parser->parse($this->path);
	}

	private function createRoute(){
		$data=$this->parseRoute(self::$parser);	//die geparste Route
		$url="";
		$var=array();
		foreach ($data[0] as $datum) {
			if(is_string($datum)){
				//füge es einfach der url wieder zu
				$url.=preg_quote($datum, '~');
				continue;
			}

			//füge var hinzu
			if(is_array($datum)){
				$var[]=$datum[0];	//varaiblen name
				$url.='('.$datum[1].')';
			}
		}

		$this->path=$url;
		$this->vars=$var;
	}

	public function addMiddleware(array $middleware,$type){
		//eine Middleware kann selber keine weiteren hinzufügen
		if($this->method===''){
			return $this;
		}

		//füge die Middleware nach vorne, damit diese route nicht überschrieben werden kann mit anderen middlewares
		MiddlewareCollector::middle($type)->add($this->path_Old,$middleware,true);

		return $this;
	}

	public static function setParser(Parser $parser){
		self::$parser=$parser;
	}
}