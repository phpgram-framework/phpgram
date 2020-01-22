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

namespace Gram\Route\Dispatcher;

/**
 * Class GroupCountBased
 * @package Gram\Route\Dispatcher\MethodSort
 *
 * Based on:
 * http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
 * https://github.com/nikic/FastRoute
 */
class GroupCountBased extends Dispatcher
{

	/**
	 * @inheritdoc
	 *
	 * Handler steht in seinem Array an der
	 *
	 * gleichen Stelle wie die Route in der Regexliste
	 *
	 * $handle= $handlerListe[Regex_Liste_Nummer][Platz_in_der_Regex]
	 *
	 * Platz in der Regex wird durch die Anzahl an matches bestimmt (die stimmt dank der Placeholder,
	 * die der Generator erstellt, überein
	 */
	public function dispatchDynamic($uri, array $routes, array $handler)
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