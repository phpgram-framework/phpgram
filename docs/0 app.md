# App and Configuration

The following configs can be changed:

- `setFactory(ResponseFactoryInterface $responseFactory)` 
	- psr 17 ResponseFactory to create the response
- `setStrategy(StrategyInterface $stdStrategy)`
	- define which [strategy](5%20strategy.md) should be used when no other strategy was defined
- `setResolverCreator(ResolverCreatorInterface $creator)`
	- define what [resolver](4%20resolver.md) should be supported
- `setLastHandler(RequestHandlerInterface $responseCreator)`
	- the [lastHandler](3%20responsecreation.md) will be executed when all [middleware](2%20mw.md) are done for this request
- `setQueueHandler(QueueHandlerInterface $queueHandler)`
	- how the middleware will be dispatched
- `setQueueClass(string $queueClass)`
	- what queue should be used (contains all middleware for this request)
- `setRouteMiddleware(MiddlewareInterface $routeMw)`
	- the middleware for invoking the [route process](1%20routes.md)
- `setContainer(ContainerInterface $container)`
	 - the Psr 15 container for [dependency management](extra/2%20di.md)
- `setRouter(RouterInterface $router)`
	- change the default router