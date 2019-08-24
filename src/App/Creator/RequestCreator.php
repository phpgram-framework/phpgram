<?php
namespace Gram\App\Creator;

class RequestCreator
{
	private $vendor=array();
	private $method, $uri, $headers=array(), $body, $version;

	public function __construct($vendor){
		$this->vendor=$vendor;
	}

	public function create(string $method, $uri, array $headers = [], $body = null, string $version = '1.1'){
		$this->method=$method;
		$this->uri=$uri;
		$this->headers=$headers;
		$this->body=$body;
		$this->version=$version;

		//wenn weitere Vendors hinzugefügt werden switch erweitern
		switch ($this->vendor){
			case "nyholm":
				return $this->createNyholm();
				break;
			case "guzzle":
				return $this->createGuzzle();
				break;
			default:
				return "";
		}
	}

	public function createNyholm(){
		return new \Nyholm\Psr7\Request($this->method,$this->uri,$this->headers,$this->body,$this->version);
	}

	public function createGuzzle(){
		return true;
	}
}