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
	/** @var array<string, array<Gram\Route\Route>> */
	protected $dynamic = [];

	/** @var array<string, array<string,int>> */
	protected $static = [];

	/** @var ParserInterface */
	protected $parser;

	public function __construct(ParserInterface $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * Generiert die Route map für die Dynamic Routes
	 *
	 * @param array $routes
	 * @return array<string, array<string, array<string|mixed>>
	 * Gebe Route und Handlerliste zurück
	 */
	abstract protected function generateDynamic(array $routes): array;

	/**
	 * @inheritdoc
	 */
	public function generate(array $routes): array
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
	protected function mapRoute(Route $route)
	{
		$data = $this->parser->parse($route->path);		//die geparsten Routedata

		$cloneRoute = false;	//muss Route clone werden

		/*
		 * durchlaufe die geparste Route
		 * sollte diese mehere Routes beinhalten (/route[/{id}])
		 *
		 * befinden sich meherere Routes im Array: /routes und /routes/id
		 */
		foreach ($data as $datum) {
			if(\count($datum) === 1 && \is_string($datum[0])) {
				$type = 0;	//static Route
			} else {
				[$path,$vars] = $this->createRoute($datum);

				if($cloneRoute === true) {
					//clone (mit neuem Pfad) die Route nur wenn sie mehrere optionale Parameter hat
					$route = $route->cloneRoute($path);
				} else {
					$route->path = $path;
					$cloneRoute = true;
				}

				$route->vars = $vars;

				$type = 1;	//dynamic
			}

			foreach ($route->method as $item) {
				if($type === 0){
					$this->static[$item][$datum[0]] = $route->routeid;
				}elseif ($type === 1){
					$this->dynamic[$item][] = $route;
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
		$url = "";
		$var = [];
		foreach ($data as $datum) {
			if(is_string($datum)){
				//füge es einfach der url wieder zu
				$url.= \preg_quote($datum, '~');
				continue;
			}

			//füge var hinzu
			if(\is_array($datum)){
				$var[] = $datum[0];	//varaiblen name
				$url.= '('.$datum[1].')';
			}
		}

		return [$url,$var];
	}
}