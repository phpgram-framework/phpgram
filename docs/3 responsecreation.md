# Response Creation

phpgram use [Psr 7](https://www.php-fig.org/psr/psr-7/) ServerRequest and Response.

At least the app needs to return a Psr 7 ResponseInterface.

The Response will be created in ResponseCreator.
This is the last handler in the [QueueHandler](2%20mw.md#queuehandler) and will be called when there is no middleware left.

The Creator will be completely configure in the [App Class](0%20app.md) at the beginning

The ResponseCreator needs:

- [Psr 17 Response Factory](https://www.php-fig.org/psr/psr-17/) to create the response

- [ResolverCreator](4%20resolver.md#resolvercreator), a [Standard Strategy](5%20strategy.md#standard-strategy) 

- (optional) a [Psr 11](https://www.php-fig.org/psr/psr-11/) for dependencies.

## The Process

1. Get [route information](2%20mw.md#route-middleware) from the request 

	1. [the route handle](1%20routes.md#route-handle)
	
	2. route parameter if the route is a [wildcard route](1%20routes.md#wildcard-routes)
	
	3. [strategy](5%20strategy.md)
	
2. Check if the strategy is a string (e.g. class name) and get it from the container.
If there is no strategy for this route the standard strategy will be used.

3. Create the response with the response factory

4. Create the [resolver](4%20resolver.md#resolvercreator) from the route handle

5. Invoke the strategy with the request, response, resolver and the route parameter

6. the strategy returns a response