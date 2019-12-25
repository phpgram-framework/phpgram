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

namespace Gram\Route\Dispatcher\Std;

/**
 * Class GroupCountBased
 * @package Gram\Route\Dispatcher\Std
 *
 * Sucht den richtigen Handler, anhand der aufgerufenen Url, in der übergebenen Regexliste
 *
 * Arbeitet nach dem Group Count Based (GCB) Prinzip
 *
 * Wird von den Router Klassen aufgerufen um ein Request durch zuführen
 *
 * Based on:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class GroupCountBased extends Dispatcher
{
	/**
	 * @inheritdoc
	 */
	public function dispatchDynamic($uri,array $routes,array $handler)
	{
		//durchlaufe die Regexlisten
		//$i = welche Regexliste
		//count($matches) = nummer des handlers
		foreach($routes as $i=>$regex) {
			if(! \preg_match($regex,$uri,$matches)){
				continue;	//wenn Route nicht Dabei ist nächsten Chunk prüfen
			}

			//wenn Regex im Chunk war
			$route = $handler[$i][count($matches)];

			$var=[];
			foreach ($route[1] as $j=>$item) {
				$var[$item]=$matches[$j+1];
			}

			return [self::FOUND,$route[0],$var];	//[status,handler,vars}
		}
		return [self::NOT_FOUND];
	}
}