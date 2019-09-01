<?php
namespace Gram\Route\Interfaces;

interface ParserInterface
{
	const DEFAULT_REGEX = '[^/]+';

	public function parse(string $route);
}