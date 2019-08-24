<?php
namespace Gram\Route\Router;
use Gram\Route\Map\MiddlewareMap;

class Routermiddle extends Router
{
	protected $options,$type;

	/**
	 * Routermiddle constructor.
	 * Läd die Routemap. Entweder wird diese durch die Klasse Route erstellt oder aus dem Cache geladen
	 * Pafd und Dateiname der zu cachenden Datei
	 * @param array $options
	 * Ein Array mit Optionen an den Router:
	 * caching = bool -> soll gecached werden
	 * cache = string -> der Pfad und Dateiname zu der Cachedatei
	 * @param $type
	 * Soll before oder after Middleware aufgerufen werden
	 */
	public function __construct(array $options,$type){
		$this->options=$options;
	}

	public function run($uri){
		$middlemap=new MiddlewareMap($this->options,$this->type);	//verfügbare Middleware Routes

		//Versuche die Route zu suchen
		if(!$this->tryDispatch($uri,$middlemap)){
			return false;
		}

		return array("handle"=>$this->handle,"param"=>$this->param);
	}
}