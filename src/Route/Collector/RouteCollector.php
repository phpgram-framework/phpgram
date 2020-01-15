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

namespace Gram\Route\Collector;

use Gram\Route\Interfaces\UtilCollectorInterface;
use Gram\Route\Route;
use Gram\Route\Interfaces\CollectorInterface;
use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\RouteGroup;

/**
 * Class RouteCollector
 * @package Gram\Route\Collector
 *
 * Wird genutzt um Routes zu definieren
 *
 * Wird beim Dispatchen aufgerufen und bereitet die Daten vor auf die der Dispatcher zugreift
 *
 * Benutzt Generatoren für die Daten
 *
 * Caching von Routes (sodass diese nicht generiert werden müssen) ist auch möglich
 */
class RouteCollector implements CollectorInterface
{
	use RouteCollectorTrait;

	protected $routes=[],$routegroupsids=[0],$basepath='',$prefix='',$er404,$er405;
	protected $routeid=0,$routegroupid=0;
	protected $caching,$cache;

	/** @var UtilCollectorInterface */
	protected $utilCollector;

	/** @var GeneratorInterface */
	protected $generator;

	/**
	 * RouteCollector constructor.
	 * @param GeneratorInterface $generator
	 * @param UtilCollectorInterface $utilCollector
	 * @param bool $routecaching
	 * @param null $routecache
	 */
	public function __construct(
		GeneratorInterface $generator,
		UtilCollectorInterface $utilCollector,
		$routecaching=false,
		$routecache=null
	){
		$this->caching=$routecaching;
		$this->cache=$routecache;
		$this->generator=$generator;
		$this->utilCollector = $utilCollector;
	}

	/**
	 * @inheritdoc
	 */
	public function add(string $path,$handler,array $method):Route
	{
		$path=$this->basepath.$this->prefix.$path;

		$route = new Route(
			$path,
			$handler,
			$method,
			$this->routegroupsids,
			$this->routeid,
			$this->utilCollector
		);

		$this->routes[$this->routeid] = $route;

		++$this->routeid;

		return $route;
	}

	/**
	 * @inheritdoc
	 */
	public function group($prefix,callable $groupcollector):RouteGroup
	{
		$pre = $this->prefix;
		$oldgroupids=$this->routegroupsids;	//Alle Gruppen in der die Route drin ist

		$this->prefix.=$prefix;
		++$this->routegroupid;
		$this->routegroupsids[]=$this->routegroupid;	//Für diese Gruppe werden allen Routes die hier drin sind die gruppenid zugeteilt

		$group = new RouteGroup($this->routegroupid,$this->utilCollector);

		\call_user_func($groupcollector);

		$this->prefix=$pre;
		$this->routegroupsids=$oldgroupids;	//stelle die alten ids wieder her da alle nachkommenden Routes nicht mehr in dieser gruppe drin sind

		return $group;
	}

	/**
	 * @inheritdoc
	 */
	public function getData():array
	{
		if($this->caching && file_exists($this->cache)){
			return require $this->cache;
		}

		$data = $this->generator->generate($this->routes);

		if($this->caching){
			\file_put_contents(
				$this->cache,
				'<?php return ' . \var_export($data, true) . ';'
			);
		}

		return $data;
	}

	/**
	 * @inheritdoc
	 */
	public function getRoute(int $routeId):Route
	{
		return $this->routes[$routeId] ?? null;
	}

	public function get404()
	{
		return $this->er404;
	}

	public function get405()
	{
		return $this->er405;
	}

	public function setBase(string $base)
	{
		$this->basepath=$base;
	}

	public function getBase()
	{
		return $this->basepath;
	}

	public function set404($handle)
	{
		$this->er404=$handle;
	}

	public function set405($handle)
	{
		$this->er405=$handle;
	}
}