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

	/** @var array */
	protected $routes = [];

	/** @var array */
	protected $routegroupsids = [0];

	/** @var string */
	protected $basepath = '';

	/** @var string */
	protected $prefix = '';

	/** @var mixed */
	protected $er404;

	/** @var mixed */
	protected $er405;

	/** @var int */
	protected $routeid = 0;

	/** @var int */
	protected $routegroupid = 0;

	/** @var GeneratorInterface */
	protected $generator;

	/**
	 * RouteCollector constructor.
	 * @param GeneratorInterface $generator
	 */
	public function __construct(GeneratorInterface $generator)
	{
		$this->generator = $generator;
	}

	/**
	 * @inheritdoc
	 */
	public function add(string $path,$handler,array $method): Route
	{
		$path = $this->basepath.$this->prefix.$path;

		$route = new Route(
			$path,
			$handler,
			$method,
			$this->routegroupsids,
			$this->routeid
		);

		$this->routes[$this->routeid] = $route;

		++$this->routeid;

		return $route;
	}

	/**
	 * @inheritdoc
	 */
	public function group($prefix,callable $groupCollector): RouteGroup
	{
		$pre = $this->prefix;
		$oldGroupIds = $this->routegroupsids;	//Alle Gruppen in der die Route drin ist

		$this->prefix.= $prefix;
		++$this->routegroupid;
		$this->routegroupsids[] = $this->routegroupid;	//Für diese Gruppe werden allen Routes die hier drin sind die gruppenid zugeteilt

		$group = new RouteGroup($this->routegroupid);

		\call_user_func($groupCollector,$this);

		$this->prefix = $pre;
		$this->routegroupsids = $oldGroupIds;	//stelle die alten ids wieder her da alle nachkommenden Routes nicht mehr in dieser gruppe drin sind

		return $group;
	}

	/**
	 * @inheritdoc
	 */
	public function getData(): array
	{
		return $this->generator->generate($this->routes);
	}

	/**
	 * @inheritdoc
	 */
	public function getRoute(int $routeId): Route
	{
		return $this->routes[$routeId] ?? null;
	}

	/**
	 * @inheritdoc
	 */
	public function get404()
	{
		return $this->er404;
	}

	/**
	 * @inheritdoc
	 */
	public function get405()
	{
		return $this->er405;
	}

	/**
	 * @inheritdoc
	 */
	public function setBase(string $base)
	{
		$this->basepath = $base;
	}

	/**
	 * @inheritdoc
	 */
	public function getBase(): string
	{
		return $this->basepath;
	}

	/**
	 * @inheritdoc
	 */
	public function set404($handle)
	{
		$this->er404 = $handle;
	}

	/**
	 * @inheritdoc
	 */
	public function set405($handle)
	{
		$this->er405 = $handle;
	}
}