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

namespace Gram\Strategy;

use Gram\Resolver\ResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class JsonStrategy
 * @package Gram\Strategy
 *
 * Strategy die das Callable ausführt und es versucht in ein Json Format zu convertieren
 */
class JsonStrategy implements StrategyInterface
{
	use StrategyTrait;

	protected $options, $depth;

	public function __construct($options = 0, $depth = 512)
	{
		$this->options=$options;
		$this->depth=$depth;
	}

	/**
	 * @inheritdoc
	 */
	public function invoke(
		ResolverInterface $resolver,
		array $param,
		ServerRequestInterface $request,
		ResponseInterface $response
	):ResponseInterface
	{
		$this->prepareResolver($request,$response,$resolver);

		$content = $resolver->resolve($param);

		if(!$this->ableToJson($content)){
			return $this->createBody($resolver,$content);
		}

		$content = \json_encode($content,$this->options,$this->depth);

		return $this->createBody($resolver,$content);
	}

	protected function getContentTypHeader(): array
	{
		return ['Content-Type','application/json'];
	}

	/**
	 * Prüft ob sich das Return des Callable in ein Json Format umwandeln lässt
	 *
	 * @param $result
	 * @return bool
	 */
	protected function ableToJson($result){
		return (\is_array($result) || \is_object($result));
	}
}