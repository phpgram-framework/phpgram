<?php
namespace Gram\Test\App;

use Gram\App\App;

/**
 * Class AppTest
 * @package Gram\Test\App
 *
 * Test für die normale App Klasse
 */
class AppTest extends AbstractAppTest
{
	protected function getApp():App
	{
		return new AppTestInit();
	}

	protected function setUp(): void
	{
		$this->initApp();
	}

}