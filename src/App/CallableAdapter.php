<?php
namespace Gram\App;
use Gram\App\Creator\ResponseCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableAdapter implements RequestHandlerInterface
{
	private $handle;

	public function __construct(callable $handle){
		$this->handle=$handle;
	}

	public function call($param,$array=false){
		if(!$array){
			return call_user_func($this->handle,$param);
		}

		return call_user_func_array($this->handle,$param);
	}

	/**
	 * @inheritdoc
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface{
		$route=$this->call($request);	//ruft Router auf: sucht den richtigen Handler anhand der Url, enthält auch den status

		$handle=$route['handle'];

		$class = CNAMESPACE.$route['handle']['c'];

		$caller=new self(array(new $class,$handle['f']));	//handler objekt mit dem die route definiert wurde
		$body=$caller->call($route['param'],true);


		$creator= new ResponseCreator("nyholm");

		$response = $creator->create($route['status'],[],$body);
	}
}