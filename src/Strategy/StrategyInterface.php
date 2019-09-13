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
 * @author Jörn Heinemann <j.heinemann1@web.de>
 */

namespace Gram\Strategy;

use Gram\Callback\CallbackInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface StrategyInterface
 * @package Gram\Strategy
 *
 * Ein Interface um Strategy aus zuführen
 */
interface StrategyInterface
{
	/**
	 * Gebe speziellen Header (Content Typ) zurück
	 *
	 * @return mixed
	 */
	public function getHeader();

	/**
	 * Führe das erhaltene Callable (von Callablecreator) aus
	 *
	 * Erstelle dann je nach Strategy den Return für den Response
	 *
	 * @param CallbackInterface $callback
	 * @param array $param
	 * @param ServerRequestInterface $request
	 * @return mixed
	 */
	public function invoke(CallbackInterface $callback, array $param, ServerRequestInterface $request);
}