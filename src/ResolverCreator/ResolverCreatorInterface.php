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

namespace Gram\ResolverCreator;

use Gram\Resolver\ResolverInterface;

/**
 * Interface CallbackCreatorInterface
 * @package Gram\CallbackCreator
 *
 * Ein Interface, dass alle Callback Creator implementiert haben müssen
 *
 * Ein Creator erstellt mithilfe von Mustererkennung ein Callback
 */
interface ResolverCreatorInterface
{
	/**
	 * Erstellt ein Callback anhand von Mustern
	 *
	 * @param $possibleCallable
	 * @return mixed
	 */
	public function createResolver($possibleCallable);

	/**
	 * Gibt ein Callback zurück
	 *
	 * @return ResolverInterface
	 */
	public function getCallable():ResolverInterface;
}