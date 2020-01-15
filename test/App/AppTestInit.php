<?php
namespace Gram\Test\App;

use Gram\App\App;
use Nyholm\Psr7\Factory\Psr17Factory;

class AppTestInit extends App
{
	public function __construct()
	{
	}

	public function build()
	{
		$factory = new Psr17Factory();

		$this->setFactory($factory);

		parent::build();
	}
}