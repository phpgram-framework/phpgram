<?php
namespace Gram\App;
use Gram\App\Creator\ResponseCreator;
use Gram\Route\Handler\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableAdapter implements RequestHandlerInterface
{

	private function call(Handler $handler,array $param){
		$callback = $handler->callback();
		return call_user_func_array($callback,$param);
	}

	/**
	 * @inheritdoc
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface{
		$status=$request->getAttribute('status',200);
		$handler=$request->getAttribute('handle');
		$param=$request->getAttribute('param');

		$body=$this->call($handler['callback'],$param);

		$creator= new ResponseCreator("nyholm");

		return $creator->create($status,[],$body);
	}
}