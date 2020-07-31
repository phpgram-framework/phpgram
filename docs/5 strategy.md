# Strategy

Strategies define in which area or environment the [route handle](1%20routes.md) inside the [resolver](4%20resolver.md)
will be executed (e.g. try catch block or in output buffer) and decided how the response body will be created from the result of the handle (e. g. convert into json string).
They will be executed in the [response handler](3%20responsecreation.md).

Strategies can also used with [dependency injection](extra/2%20di.md)

Strategies needs to implement the following interface: `Gram\Strategy\StrategyInterface`

Strategies can be used for [routes or route groups](1%20routes.md). Only the first strategy will be used for the route or group.

````php
<?php
use Gram\App\App;
use Gram\Strategy\StdAppStrategy;
use Gram\Strategy\BufferAppStrategy;

//group
App::app()->group("/prefix",function (){
	//route
	App::app()->get("/function/{id}",function ($id){
    	return "ID = ".$id;
    })->addStrategy(new StdAppStrategy());
	
	App::app()->post("/create",function (){
		echo "created";
	});
	
})->addStrategy(BufferAppStrategy::class);	//use DI
````

- `/prefix/function/123` will use the `StdAppStrategy`
- `/prefix/create` will use the `BufferAppStrategy`

## Standard Strategy

It is possible to create own strategies for different use cases. Phpgram comes with three default strategies.

### StdAppStrategy

The StdAppStrategy just invoke the route handle and expect a string.
If you want to return other formats you need to create your own strategy. 

The default content-type is `text/html`

### BufferAppStrategy

This strategy works like StdAppStrategy but the invoke is different. It invoked the handle inside a output buffer. So all echos will be catched.
`header()` will be catched aswell!

### JsonStrategy

This strategy converts the return of the handle to a json string. So it isn't necessary to use json_encode for every return from the handle.

The default content-type is `application/json`

## Simple Example

````php
<?php

namespace Strategy;

use Gram\Resolver\ResolverInterface;
use Gram\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class JsonAppStrategy implements StrategyInterface
{
	
	/**
	 * @inheritdoc
	 */
	public function invoke(
		ResolverInterface $resolver,
		array $param,
		ServerRequestInterface $request,
		ResponseInterface $response
	):ResponseInterface
	{
		//first of all the resolver can be prepared like: set the right content-type into the response

		$resolver->setRequest($request);
		$resolver->setResponse($response);

		//the resolver will be executed. e.g. in try catch block
		try {
			$content = $resolver->resolve($param);
		} catch (\Exception $e) {
			//exceptions will be valid json format
			$content = ["error"=>$e->getMessage(),"status"=>false];
		}
		
		//check if return is a custom response
		if($content instanceof ResponseInterface) {
			return $response;
		}
		
		//the return must be placed inside the response body
		
		$response = $resolver->getResponse(); //maybe the handle changed the response e.g. other status
		
		//encode th return to json
		$response->getBody()->write(json_encode($content));
		
		return $response;
	}
}
````