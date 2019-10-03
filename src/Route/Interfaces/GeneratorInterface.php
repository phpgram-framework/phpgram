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

/**
 * Interface GeneratorInterface
 * @package Gram\Route\Interfaces
 *
 * Ein Interface für alle Generatoren
 */
interface GeneratorInterface
{
	public function generate(array $routes);
	public function generateDynamic(array $routes);
}