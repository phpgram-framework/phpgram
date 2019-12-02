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

namespace Gram\Route;

use Gram\Route\Interfaces\MiddlewareCollectorInterface;
use Gram\Route\Interfaces\ParserInterface;
use Gram\Route\Interfaces\StrategyCollectorInterface;

/**
 * Class Route
 * @package Gram\Route
 *
 * Ein Routeobjekt, dass Informationen über die Route enthält
 *
 * wird vom Routecollecotr aufgerufen wenn eine neue Route hinzugefügt wird
 *
 * Bietet die Möglichkeit Middleware und Strategy für die Route hinzu zufügen
 *
 * Parst auch die Route mithilfe des Parsers
 */
class Route
{
	public $path,$handle,$vars,$parser,$stack,$strategyCollector,$groupid,$routeid,$method;

	/**
	 * Route constructor.
	 * @param string $path
	 * @param $method
	 * @param $routegroupid
	 * @param $routeid
	 * @param ParserInterface $parser
	 * @param MiddlewareCollectorInterface $stack
	 * @param StrategyCollectorInterface $strategyCollector
	 */
	public function __construct(
		string $path,
		$method,
		$routegroupid,
		$routeid,
		ParserInterface $parser,
		MiddlewareCollectorInterface $stack,
		StrategyCollectorInterface $strategyCollector
	){
		$this->method=$method;	//speichere Method für Dispatcher
		$this->handle['groupid']=$routegroupid;
		$this->handle['routeid']=$routeid;

		$this->path=$path;
		$this->groupid=$routegroupid;
		$this->routeid=$routeid;
		$this->parser=$parser;
		$this->stack=$stack;
		$this->strategyCollector=$strategyCollector;
	}

	private function parseRoute(ParserInterface $parser)
	{
		return $parser->parse($this->path);
	}

	/**
	 * Parst die Route für die Parameter
	 */
	public function createRoute()
	{
		$data=$this->parseRoute($this->parser);	//die geparste Route
		$url="";
		$var=[];
		foreach ($data[0] as $datum) {
			if(is_string($datum)){
				//füge es einfach der url wieder zu
				$url.= \preg_quote($datum, '~');
				continue;
			}

			//füge var hinzu
			if(\is_array($datum)){
				$var[]=$datum[0];	//varaiblen name
				$url.='('.$datum[1].')';
			}
		}

		$this->path=$url;
		$this->vars=$var;
	}

	/**
	 * Kann nach dem definieren einer Route aufgerufen werden um mehre Middleware hinzu zufügen
	 *
	 * @param $middleware
	 * @return $this
	 */
	public function addMiddleware($middleware)
	{
		$this->stack->addRoute($this->routeid,$middleware);

		return $this;
	}

	public function addStrategy($strategy)
	{
		$this->strategyCollector->addRoute($this->routeid,$strategy);

		return $this;
	}
}