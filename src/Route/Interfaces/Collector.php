<?php
namespace Gram\Route\Interfaces;

/**
 * Interface Collector
 * @package Gram\Route\Collector
 * @author Jörn Heinemann
 * Interface das alle Collectoren implementiert haben müssen
 * Somit können auch andere Collectoren genutzt werden
 */
interface Collector
{
	public function map();
	public function trigger();
	public function setGroup($prefix,callable $callback);
	public function set($path,$handle,$method,$atFirst=false);
}