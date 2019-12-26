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
	const CHUNKSIZE = 10;

	protected $dynamic = [];
	protected $static = [];

	/** @var ParserInterface */
	protected $parser;

	public function __construct(ParserInterface $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * Trenne die Routes in Static und Dynamic auf
	 *
	 * @param Route $route
	 * @return mixed
	 */
	abstract public function mapRoute(Route $route);

	/**
	 * @inheritdoc
	 */
	public function generate(array $routes)
	{
		foreach ($routes as $route) {
			$this->mapRoute($route);
		}

		return ['static'=>$this->static,'dynamic'=>$this->generateDynamic($this->dynamic)];
	}

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