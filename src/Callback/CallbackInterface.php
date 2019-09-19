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

namespace Gram\Callback;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface Callback
 * @package Gram\Callback
 *
 * Interface für die Callback Klassen die ein Callable erstellen und ausführen
 */
interface CallbackInterface
{
	public function callback($param=[],ServerRequestInterface $request);

	public function getRequest():ServerRequestInterface;

	public function set();
}