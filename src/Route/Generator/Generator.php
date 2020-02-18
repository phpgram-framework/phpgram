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
	public function generate(array $routes):array
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
		$data = $this->parser->parse($route->path);	//die geparsten Routedata

		$dynamicCounter = 0;	//muss Route clone werden

		//durchlaufe die geparste Route, sollte diese mehere Routes beinhalten (/route[/{id}] -> zweites Array) wird diese extra hinzugefügt
		foreach ($data as $datum) {
			if(\count($datum) === 1 && \is_string($datum[0])) {
				$type = 0;	//static Route
			} else {
				//soltle die Route eine dynamic Route sein, clone diese mit den neuen path
				[$path,$vars] = $this->createRoute($datum);

				if($dynamicCounter > 0) {
					//clone die Route nur wenn sie mehrere optionale Parameter hat
					$route = $route->cloneRoute($path);
				} else {
					$route->path = $path;
				}

				$route->vars = $vars;

				$type= 1;	//dynamic
				++$dynamicCounter;
			}

			foreach ($route->method as $item) {
				if($type===0){
					$this->static[$item][$datum[0]]=$route->routeid;
				}elseif ($type===1){
					$this->dynamic[$item][]=$route;
				}
			}
		}
	}

	/**
	 * Verarbeite die geparste Route
	 *
	 * @param array $data
	 * @return array
	 */
	protected function createRoute(array $data)
	{
		$url="";
		$var=[];
		foreach ($data as $datum) {
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