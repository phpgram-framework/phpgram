<?php
namespace Gram\Route\Collector;
use Gram\Route\Route;
use Gram\Route\Interfaces\CollectorInterface;
use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\ParserInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;
use Gram\Route\RouteGroup;

class RouteCollector implements CollectorInterface
{
	private $routes=[],$routegroupsids=[0],$basepath='',$prefix='',$er404,$er405;
	private $handler=[],$routeid=0,$routegroupid=0;
	private $parser,$generator,$caching,$cache,$stack,$strategyCollector;

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

	public function get(string $route,$handler)
	{
		return $this->add($route,$handler,['GET']);
	}

	public function post(string $route,$handler)
	{
		return $this->add($route,$handler,['POST']);
	}

	public function getpost(string $route,$handler)
	{
		return $this->add($route,$handler,['GET','POST']);
	}

	public function head(string $route,$handler)
	{
		return $this->add($route,$handler,['HEAD']);
	}

	public function delete(string $route,$handler)
	{
		return $this->add($route,$handler,['DELETE']);
	}

	public function put(string $route,$handler)
	{
		return $this->add($route,$handler,['PUT']);
	}

	public function patch(string $route,$handler)
	{
		return $this->add($route,$handler,['PATCH']);
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