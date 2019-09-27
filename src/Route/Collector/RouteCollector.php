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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Route\Collector;

use Gram\Route\Route;
use Gram\Route\Interfaces\CollectorInterface;
use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\ParserInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;
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

	private $routes=[],$routegroupsids=[0],$basepath='',$prefix='',$er404,$er405;
	private $handler=[],$routeid=0,$routegroupid=0;
	private $parser,$generator,$caching,$cache,$stack,$strategyCollector;

	/**
	 * RouteCollector constructor.
	 * @param ParserInterface $parser
	 * @param GeneratorInterface $generator
	 * @param MiddlewareCollectorInterface $stack
	 * @param StrategyCollectorInterface $strategyCollector
	 * @param bool $routecaching
	 * @param null $routecache
	 */
	public function __construct(
		ParserInterface $parser,
		GeneratorInterface $generator,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector,
		$routecaching=false,
		$routecache=null
	){
		$this->parser=$parser;
		$this->caching=$routecaching;
		$this->cache=$routecache;
		$this->generator=$generator;
		$this->stack=$stack;
		$this->strategyCollector=$strategyCollector;
	}

	/**
	 * @inheritdoc
	 */
	public function add(string $path,$handler,array $method):Route
	{
		$path=$this->basepath.$this->prefix.$path;

		$this->handler[$this->routeid]=$handler;

		$route = new Route(
			$path,
			$method,
			$this->routegroupsids,
			$this->routeid,
			$this->parser,
			$this->stack,
			$this->strategyCollector
		);

		$this->routes[$this->routeid] = $route;

		++$this->routeid;

		return $route;
	}

	/**
	 * @inheritdoc
	 */
	public function addGroup($prefix,callable $groupcollector):RouteGroup
	{
		$pre = $this->prefix;
		$oldgroupids=$this->routegroupsids;

		$this->prefix=$this->prefix.$prefix;
		$this->routegroupid=$this->routegroupid+1;
		$this->routegroupsids[]=$this->routegroupid;

		$group = new RouteGroup($this->prefix,$this->routegroupid,$this->stack,$this->strategyCollector);

		call_user_func($groupcollector);

		$this->prefix=$pre;
		$this->routegroupsids=$oldgroupids;

		return $group;
	}

	/**
	 * @inheritdoc
	 */
	public function getData()
	{
		if(file_exists($this->cache)){
			return require $this->cache;
		}

		$data = $this->generator->generate($this->routes);

		if($this->caching){
			file_put_contents(
				$this->cache,
				'<?php return ' . var_export($data, true) . ';'
			);
		}

		return $data;
	}

	public function getHandle()
	{
		return $this->handler;
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

	public function set404($handle)
	{
		$this->er404=$handle;
	}

	public function set405($handle)
	{
		$this->er405=$handle;
	}
}