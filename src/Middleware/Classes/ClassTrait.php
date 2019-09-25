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

namespace Gram\Middleware\Classes;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trait ControllerTrait
 * @package Gram\Middleware\Controller
 *
 * Hilfstrait um die Psr Funktionen zu implementieren
 */
trait ClassTrait
{
	/** @var ServerRequestInterface */
	protected $request;

	/** @var ResponseInterface */
	protected $response;

	/** @var ContainerInterface */
	protected $psr11Container;

	/**
	 * @inheritdoc
	 */
	public function setPsr(ServerRequestInterface $request, ResponseInterface $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * @inheritdoc
	 */
	public function getRequest():ServerRequestInterface
	{
		return $this->request;
	}

	/**
	 * @inheritdoc
	 */
	public function getResponse():ResponseInterface
	{
		return $this->response;
	}

	/**
	 * @inheritdoc
	 */
	public function setContainer(ContainerInterface $container=null)
	{
		$this->psr11Container = $container;
	}

	/**
	 * @inheritdoc
	 */
	public function __get($name)
	{
		if($this->psr11Container===null){
			return null;
		}

		return $this->psr11Container->get($name);
	}
}