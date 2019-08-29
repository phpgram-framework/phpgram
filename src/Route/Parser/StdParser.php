<?php
namespace Gram\Route\Parser;
use Gram\Route\Interfaces\Parser;
/**
 * Class StdParser
 * @package Gram\Route\Parser
 */
class StdParser implements Parser
{
	/**
	 * @copyright Nikita Popov (FastRoute <https://github.com/nikic/FastRoute>)
	 */
	const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;

	/**
	 * @copyright Phil Bennett philipobenito@gmail.com (thephpleague <https://route.thephpleague.com/>)
	 */
	private static $placeholders=array(
		'/{(.+?):n}/'=>'{$1:[0-9]+}',		//Zahlen
		'/{(.+?):a}/'=>'{$1:[0-9,a-z,A-Z_äÄöÖüÜß]+}',	//Umlaute und dash
		'/{(.+?):all}/'=>'{$1:.+?}'	//matche alles sowie den backslash
	);

	private $uri;

	public function parse(string $route){
		$this->uri=$route;

		return $this->parsePlaceholders();
	}

	private function parsePlaceholders(){
		/**
		 * @copyright Phil Bennett philipobenito@gmail.com (thephpleague <https://route.thephpleague.com/>)
		 */
		$data=preg_replace(array_keys(self::$placeholders), array_values(self::$placeholders), $this->uri);	//Costume Placeholder


		/**
		 * @copyright Nikita Popov (FastRoute <https://github.com/nikic/FastRoute>)
		 */
		$routeWithoutClosingOptionals = rtrim($data, ']');
		$segments = preg_split('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);

		$currentRoute = '';
		$routeDatas = [];
		foreach ($segments as $n => $segment) {
			if ($segment === '' && $n !== 0) {
				continue;
			}

			$currentRoute .= $segment;
			$routeDatas[] = $this->createVars($currentRoute);
		}

		return $routeDatas;
	}

	private function createVars($route){
		/**
		 * @copyright Nikita Popov (FastRoute <https://github.com/nikic/FastRoute>)
		 */
		if (!preg_match_all(
			'~' . self::VARIABLE_REGEX . '~x', $route, $matches,
			PREG_OFFSET_CAPTURE | PREG_SET_ORDER
		)) {
			return array($route);
		}

		$offset = 0;
		$routeData = [];
		foreach ($matches as $set) {
			if ($set[0][1] > $offset) {
				$routeData[] = substr($route, $offset, $set[0][1] - $offset);
			}
			$routeData[] = [
				$set[1][0],
				isset($set[2]) ? trim($set[2][0]) : self::DEFAULT_REGEX
			];
			$offset = $set[0][1] + strlen($set[0][0]);
		}

		if ($offset !== strlen($route)) {
			$routeData[] = substr($route, $offset);
		}

		return $routeData;
	}

	public static function addDataTyp(string $typ,string $regex){
		/**
		 * @copyright Phil Bennett philipobenito@gmail.com (thephpleague <https://route.thephpleague.com/>)
		 */
		$pattern='/{(.+?):'.$typ.'}/';
		$regex='{$1:'.$regex.'}';

		self::$placeholders[$pattern] = $regex;
	}
}