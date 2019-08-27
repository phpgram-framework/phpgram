<?php
namespace Gram\Middleware;

use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{
	protected $request;

	public function __construct(ServerRequestInterface $request){
		$this->request=$request;

		//debug_page($request);
	}
}