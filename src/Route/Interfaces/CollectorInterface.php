<?php
namespace Gram\Route\Interfaces;
use Gram\Route\Route;
use Gram\Route\RouteGroup;

interface CollectorInterface
{
	public function add(string $path,$handler,array $method):Route;
	public function addGroup($prefix,callable $groupcollector):RouteGroup;
	public function getData();
	public function getHandle();
	public function get404();
	public function get405();
	public function set404($handle);
	public function set405($handle);
	public function get(string $route,$handler);
	public function post(string $route,$handler);
	public function getpost(string $route,$handler);
	public function head(string $route,$handler);
	public function delete(string $route,$handler);
	public function put(string $route,$handler);
	public function patch(string $route,$handler);
	public function setBase(string $base);
}