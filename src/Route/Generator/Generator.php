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

namespace Gram\Route\Generator;

use Gram\Route\Interfaces\GeneratorInterface;
use Gram\Route\Interfaces\ParserInterface;
use Gram\Route\Route;

/**
 * Class Generator
 * @package Gram\Route\Generator
 *
 * Hauptgenerator wird für die static Routes genutzt
 *
 * Trennt zuerst die static von den dynamischen Routes
 *
 * Fügt die static Routes dem Array hinzu
 *
 * Führt danach den Dynamischen Generator aus
 */
abstract class Generator implements GeneratorInterface
{
	protected $dynamic = [];
	protected $static = [];

	/** @var ParserInterface */
	protected $parser;

	public function __construct(ParserInterface $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * @inheritdoc
	 */
	public function generate(array &$routes):array
	{
		foreach ($routes as $route) {
			$this->mapRoute($route);
		}

		return ['static'=>$this->static,'dynamic'=>$this->generateDynamic($this->dynamic)];
	}

	/**
	 * Trenne die Routes in Static und Dynamic auf
	 *
	 * @param Route $route
	 * @return void
	 */
	public function mapRoute(Route $route)
	{
		[$route->path,$route->vars] = $this->createRoute($route->path);	//parse die Route

		//Ordne die Route in Static und Dynamic
		$typ = \count($route->vars) === 0 ? 0 : 1;

		foreach ($route->method as $item) {
			if($typ===0){
				$this->static[$item][$route->path]=$route->routeid;
			}elseif ($typ===1){
				$this->dynamic[$item][]=$route;
			}
		}
	}

	/**
	 * Verarbeite die geparste Route
	 *
	 * @param string $path
	 * @return array
	 */
	protected function createRoute(string $path)
	{
		$data=$this->parser->parse($path);	//die geparste Route
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

		return [$url,$var];
	}
}