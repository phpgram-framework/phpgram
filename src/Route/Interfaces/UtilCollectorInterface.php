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

namespace Gram\Route\Interfaces;


interface UtilCollectorInterface
{
	public function collect($key, $value);

	public function collectSingle($key, $value);

	public function route($routeId, $key, $value);

	public function routeSingle($routeId, $key, $value);

	public function group($groupId, $key, $value);

	public function groupSingle($groupId, $key, $value);

	public function get($key);

	public function getRoute($routeId, $key);

	public function getGroup($groupId, $key);
}