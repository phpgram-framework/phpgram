<?php
namespace Gram\App\Creator;

class ResponseCreator
{
	private $vendor=array();
	private $status, $headers=array(),$body,$version,$reason;

	public function __construct($vendor){
		$this->vendor=$vendor;
	}

	public function create(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = null){
		$this->status=$status;
		$this->headers=$headers;
		$this->body=$body;
		$this->version=$version;
		$this->reason=$reason;

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
		return new \Nyholm\Psr7\Response($this->status,$this->headers,$this->body,$this->version,$this->reason);
	}

	public function createGuzzle(){
		return true;
	}
}