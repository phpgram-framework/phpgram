<?php
/**
 * phpgram
 *
 * This File is part of the phpgram Micro Framework
 *
 * Web: https://gitlab.com/grammm/php-gram/phpgram
 *
 * @license https://gitlab.com/grammm/php-gram/phpgram/blob/master/LICENSE
 *
 * @author Jörn Heinemann <joernheinemann@gmx.de>
 */

namespace Gram\Test\App;
use Gram\Async\App\AsyncApp;
use Nyholm\Psr7\Factory\Psr17Factory;

class AsyncAppTestInit extends AsyncApp
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
}