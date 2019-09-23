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

namespace Gram\Resolver;

use Gram\Middleware\Classes\ClassInterface;

/**
 * Class ControllerHandler
 * @package Gram\Resolver
 *
 * Konvertiert folgendes Muster: Controller@function zu class= Controller Function = function
 *
 * Erstellt dann den Handler mit dem ClassHandler
 */
class ClassResolver implements ResolverInterface
{
	use ResolverTrait;

	protected $classname,$function,$param;

	/** @var \ReflectionClass */
	protected $reflector;

	/** @var ClassInterface */
	protected $class;


	/**
	 * @inheritdoc
	 *
	 * Führt die Class aus und gibt dessen Return zurück
	 *
	 * Gibt eine Exception aus sollte die auszuführende Klasse kein @see ClassInterface implementiert haben
	 *
	 * @param array $param
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function resolve($param=[])
	{
		$this->reflector = new \ReflectionClass($this->classname);

		if(!$this->reflector->isInstantiable()) {
			throw new \Exception("[$this->classname] is not instantiable");
		}

		if(!$this->reflector->implementsInterface('Gram\Middleware\Classes\ClassInterface')){
			throw new \Exception("[$this->classname] needs to implement Gram\Middleware\Classes\ClassInterface");
		}


		$this->class = $this->getClass();

		$this->class->setPsr($this->request,$this->response);	//gebe den Klassen die Psr Objekte
		$callback = [$this->class,$this->function];

		$return = call_user_func_array($callback,$param);

		$this->request = $this->class->getRequest();	//nehme Request wieder entgegen
		$this->response = $this->class->getResponse();

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
	 * @return object
	 * @throws \Exception
	 */
	protected function getClass()
	{
		$constructor = $this->reflector->getConstructor();

		//wenn es keinen Construktor oder Container für DI gibt gebe die neue Klasse zurück
		if($constructor===null || $this->container === null)
		{
			return new $this->classname;
		}

		$con_param = $constructor->getParameters();
		$dependencies = $this->getDependencies($con_param);

		return $this->reflector->newInstanceArgs($dependencies);
	}

	/**
	 * @param array $parameters
	 * @return array
	 * @throws \Exception
	 */
	protected function getDependencies(array $parameters)
	{
		$dependencies = [];

		foreach($parameters as $parameter)
		{
			$dependencies[] = $this->resolveParam($parameter);
		}

		return $dependencies;
	}

	/**
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 * @throws \Exception
	 */
	protected function resolveParam(\ReflectionParameter $parameter)
	{
		//Suche zuerst Dep ohne Namespace
		$dependency_short = $parameter->getClass()->getShortName();
		if($this->container->has($dependency_short)){
			return $this->container->get($dependency_short);
		}

		//Suche dann Dep mit Namespace
		$dependency = $parameter->getClass()->getName();
		if($this->container->has($dependency)){
			return $this->container->get($dependency);
		}

		//Prüfe dann ob es einen Defaultwert gibt, wenn ja setze diesen
		if($parameter->isDefaultValueAvailable()) {
			return $parameter->getDefaultValue();
		}

		throw new \Exception("Dependency [$dependency] for [$this->classname] is missing");
	}

	/**
	 * Nimmt eine Class entgegen
	 *
	 * trennt den Class String in Klasse und Funktion
	 *
	 * @param string $controller
	 * @throws \Exception
	 */
	public function set($controller="")
	{
		if($controller===""){
			throw new \Exception("Keinen Controller angegeben");
		}

		$extract = explode('@',$controller);

		if($extract[0]==="" || $extract[1]===""){
			throw new \Exception("Keine Klasse oder Funktion angegeben");
		}

		$this->classname=$extract[0];
		$this->function=$extract[1];
	}
}