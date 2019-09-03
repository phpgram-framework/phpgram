<?php
namespace Gram\Strategy;

use Gram\Callback\Callback;
use Psr\Http\Message\ServerRequestInterface;

class JsonStrategy implements StrategyInterface
{
	public function getHeader()
	{
		return ["name"=>'Content-Type',"value"=>'application/json'];
	}

	public function invoke(Callback $callback, array $param, ServerRequestInterface $request)
	{
		$result = $callback->callback($param,$request);

		if(!$this->ableToJson($result)){
			return $result;
		}

		return json_encode($result);
	}

	private function ableToJson($result){
		return (is_array($result) || is_object($result));
	}
}