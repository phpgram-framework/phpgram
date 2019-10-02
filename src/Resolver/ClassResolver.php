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

		//gebe den Klassen die Psr Objekte
		$this->class->setPsr($this->request,$this->response);
		$this->class->setContainer($this->container);

		$callback = [$this->class,$this->function];

		$return = call_user_func_array($callback,$param);

		$this->response = $this->class->getResponse();

		return ($return===null)?'':$return;	//default: immer einen String zurück geben
	}

	/**
	 * Erstellt das Class Object
	 *
	 * Entweder direkt, aus dem Container oder mit den Dependencies des Class Constructor
	 *
	 * @return object
	 * @throws \Exception
	 */
	protected function getClass()
	{
		//wenn es keinen Container gibt gebe das neue object zurück
		if($this->container === null){
			return new $this->classname;
		}

		$constructor = $this->reflector->getConstructor();

		//Wenn es die Klasse bereits im Container gibt gebe dieses Object zurück, hier mit Namespace
		if($this->container->has($this->classname)){
			return $this->container->get($this->classname);
		}

		//wenn es keinen Construktor für DI gibt gebe die neue Klasse zurück
		if($constructor===null) {
			return new $this->classname;
		}

		$con_param = $constructor->getParameters();
		$dependencies = $this->getDependencies($con_param);

		return $this->reflector->newInstanceArgs($dependencies);	//erstelle das Object mit den Parameter im Constructor
	}

	/**
	 * Durchläuft alle Dependencies der Klasse
	 * use gibt die Parameter als Array zurück
	 *
	 * Benutzt dazu die Hilfs Method @see resolveParam
	 * die bei jedem Parameter aufgerufen wird und die jeweilige Dependency aus dem Container läd
	 *
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
	 * Bekommt einen Parameter übergeben und sucht diesen im Psr 11 Container
	 *
	 * Suche zuerst nach Dependency mit Namespace im Container
	 *
	 * Danach suche nach der Dependency ohne Namespace
	 *
	 * Wenn nichts gefunden wurde, prüfe ob es einen Default Value gibt,
	 * wenn ja gebe diesen zurück
	 *
	 * Wenn nicht throw Exception
	 *
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 * @throws \Exception
	 */
	protected function resolveParam(\ReflectionParameter $parameter)
	{
		//Suche zuerst Dep mit Namespace
		$dependency = $parameter->getClass()->getName();
		if($this->container->has($dependency)){
			return $this->container->get($dependency);
		}

		//Suche danach Dep ohne Namespace
		$dependency_short = $parameter->getClass()->getShortName();
		if($this->container->has($dependency_short)){
			return $this->container->get($dependency_short);
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