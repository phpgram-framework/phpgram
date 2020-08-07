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
 * Interface ParserInterface
 * @package Gram\Route\Interfaces
 *
 * Ein Interface für alle Parser
 */
interface ParserInterface
{
	const DEFAULT_REGEX = '[^/]+';

	/**
	 * Parse die Route
	 *
	 * Wandle die Placeholder um
	 *
	 * Gebe die Route mit ihren Bestandteilen zurück
	 *
	 * @param string $route
	 * @return mixed
	 */
	public function parse(string $route);
}