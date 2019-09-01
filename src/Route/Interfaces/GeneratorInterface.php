<?php
namespace Gram\Route\Interfaces;

interface GeneratorInterface
{
	public function generate(array $routes);
	public function generateDynamic(array $routes);
}