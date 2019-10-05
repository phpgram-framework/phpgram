<?php
namespace Gram\Test\TestClasses;

use Gram\Middleware\Classes\ClassTrait;
use Gram\Middleware\Handler\HandlerInterface;

class TestHandlerClass implements HandlerInterface
{
	use ClassTrait;

	public function handle()
	{
		$this->response = $this->response->withStatus(404);

		$callTest = $this->request->getAttribute('testCall');

		return $callTest;
	}
}