<?php
namespace Gram\Test\App;

use Gram\App\QueueHandler;
use Gram\Middleware\Handler\ResponseCreator;
use Gram\Route\Collector\MiddlewareCollector;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Container\ContainerInterface;

class AppGeneralTest extends TestCase
{
	/** @var AppTestInit */
	protected $app;
	/** @var QueueHandler */
	protected $queue;
	/** @var ResponseCreator */
	protected $lastHandler;

	public function init()
	{
		$this->app = new AppTestInit();
		$this->queue = $this->app->init();
		$this->lastHandler = $this->queue->getLast();
	}

	public function testStdInitOfQueue()
	{
		$this->init();

		self::assertInstanceOf(QueueHandler::class,$this->queue);
	}

	public function testStdInitOfResponseCreator()
	{
		$this->init();

		self::assertInstanceOf(ResponseCreator::class,$this->lastHandler);
	}

	public function testStdInitOfDependencies()
	{
		$this->init();

		$container = new Container();

		$psr11 = new \Pimple\Psr11\Container($container);

		$this->app->setContainer($psr11);

		$mwcollector = $this->app->getMiddlewareCollector();
		$containerNotSetYet = $this->app->getContainer();

		self::assertInstanceOf(ContainerInterface::class,$containerNotSetYet);
		self::assertInstanceOf(MiddlewareCollector::class,$mwcollector);
	}


}