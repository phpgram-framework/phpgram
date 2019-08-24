<?php
namespace Gram\Route\Router;
use Gram\Route\Handler\Handler;
use Gram\Route\Map\RouteMap;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RouterRoute
 * @package Gram\Lib\Route
 * @author Jörn Heinemann
 * Sucht die richtige Route anhand der aufgerufenen Url mithilfe eines Dispatcher
 */
class RouterRoute extends Router
{
	protected $options,$routemap;

	/**
	 * RouterRoute constructor.
	 * Läd die Routemap. Entweder wird diese durch die Klasse Route erstellt oder aus dem Cache geladen
	 * Pafd und Dateiname der zu cachenden Datei
	 * @param array $options
	 * Ein Array mit Optionen an den Router:
	 * caching = bool -> soll gecached werden
	 * cache = string -> der Pfad und Dateiname zu der Cachedatei
	 * checkRm = bool -> soll die Routemethode (z. B. get oder post) überprüft werden
	 * definePaths = array() -> Die path in die der Collector nach Routes sehen soll
	 */
	public function __construct(array $options){
		$this->options=$options;
		$this->routemap=new RouteMap($options);
	}

	/**
	 * Vergleicht die aufgerufene Url mit den verfügbaren mit Hilfe von Dispatcher
	 * Verfügbare Urls werden entweder als Static Urls oder mit Regex bestimmt
	 * Der/Die Suchwert steht in $matches drin und wird an die Funktion in der Klasse übergeben
	 *
	 * Bsp Url:
	 * /video/{id}
	 * hier werden nach /video/ alle Zahlen von 0 - 9 akzeptiert
	 * die Parameter sind dann die Zahl und die Buchstabencombination
	 *
	 * @param string $uri
	 * Die Url die requestet wurde
	 * @param $httpMethod
	 * die Methode der Url
	 * @return bool
	 */
	public function run($uri,$httpMethod){

		//debug_page($this->routemap->getMap());

		//Versuche die Route zu suchen
		if(!$this->tryDispatch($uri,$this->routemap)){
			$map=$this->routemap->getMap();
			//$this->handle=$map['er404'];
			return false;
		}

		//Prüfe die Methode
		if($this->options['checkRm'] && !$this->checkMethod($httpMethod)){
			$map=$this->routemap->getMap();
			//$this->handle=$map['erNotAllowed'];
			return false;
		}

		$this->status=self::OK;
		return true;
	}

	/**
	 * Ein Rotuer für Psr Requests
	 * @param ServerRequestInterface $request
	 * @return array
	 */
	public function psrRun(ServerRequestInterface $request){
		$httpMethod = $request->getMethod();
		$uri = $request->getUri()->getPath();

		$this->run($uri, $httpMethod);

		return array('status'=>$this->status,'handle'=>$this->handle,'param'=>$this->param);
	}

	/**
	 * Ein Rotuer ohne psr 7
	 * @param $uri
	 * @param $httpMethod
	 * @return array
	 */
	public function normalRun($uri,$httpMethod){
		$this->run($uri,$httpMethod);

		return array("handle"=>$this->handle['callback'],"param"=>$this->param);
	}
}