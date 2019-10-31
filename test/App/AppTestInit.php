<?php
namespace Gram\Test\App;

use Gram\App\App;
use Nyholm\Psr7\Factory\Psr17Factory;

class AppTestInit extends App
{
	public function __construct()
	{
	}

	public function building()
	{
		$factory = new Psr17Factory();

		$this->setFactory($factory,$factory);

		parent::build();
	}

	public function getMiddlewareCollector()
	{
		return $this->middlewareCollector;
	}

	public function getStrategy()
	{
		return $this->stdStrategy;
	}

	public function getContainer()
	{
		return $this->container;
	}

	public function getResolveCreator()
	{
		return $this->resolverCreator;
	}

	public function init()
	{
		parent::init();
	}
}