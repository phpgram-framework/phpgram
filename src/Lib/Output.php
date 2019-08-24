<?php
namespace Gram\Lib;

/**
 * Class Output
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 1.0
 * Outputklasse die eine simple Middleware darstellt
 */
class Output
{
	public function template($echo){
		echo $echo;
		return true;
	}

	public function json($echo,$return=true){
		$json = json_encode($echo);

		if($return){
			return $json;
		}else{
			echo $json;
		}
	}
}