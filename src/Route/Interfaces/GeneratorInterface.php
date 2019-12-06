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
	/**
	 * Bereitet die Routes vor
	 * und unterteilt diese in Static und Dynamic Routes
	 *
	 * @param array $routes
	 * @return mixed
	 */
	public function generate(array $routes);

	/**
	 * Generiert die Route map für die Dynamic Routes
	 *
	 * @param array $routes
	 * @return mixed
	 */
	public function generateDynamic(array $routes);
}