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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Strategy;

use Gram\Callback\Callback;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class JsonStrategy
 * @package Gram\Strategy
 *
 * Strategy die das Callable ausführt und es versucht in ein Json Format zu convertieren
 */
class JsonStrategy implements StrategyInterface
{
	/**
	 * @inheritdoc
	 */
	public function getHeader()
	{
		return ["name"=>'Content-Type',"value"=>'application/json'];
	}

	/**
	 * @inheritdoc
	 */
	public function invoke(Callback $callback, array $param, ServerRequestInterface $request)
	{
		$result = $callback->callback($param,$request);

		if(!$this->ableToJson($result)){
			return $result;
		}

		return json_encode($result);
	}

	/**
	 * Prüft ob sich das Return des Callable in ein Json Format umwandeln lässt
	 *
	 * @param $result
	 * @return bool
	 */
	private function ableToJson($result){
		return (is_array($result) || is_object($result));
	}
}