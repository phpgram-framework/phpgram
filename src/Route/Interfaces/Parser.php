<?php
namespace Gram\Route\Interfaces;

interface Parser
{
	const DEFAULT_REGEX = '[^/]+';

	public function parse(string $route);
}