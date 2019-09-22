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

/**
 * Allgemeine Funktionen die keine Klasse brauchen
 */

if(!function_exists('debug_console')){
	/*
	 * Einfache Debugausgabe in die js Console
	 *
	 * @param $data
	 */
	function debug_console($data) {
		if (is_array($data))
			$output = "<script>console.log('Debugausgabe: ".implode(',', $data). "');</script>";
		else
			$output = "<script>console.log('Debugausgabe: ".$data."');</script>";

		echo $output;
	}
}

if(!function_exists('debug_page')){
	/**
	 * Macht einen var_dumb mit <pre></pre> bzw ein print_r von daten
	 *
	 * @param $data
	 * @param bool $vd
	 */
	function debug_page($data,$vd=false){
		echo "<pre>";
		if($vd){
			var_dump($data);
		}else{
			print_r($data);
		}
		echo "</pre>";
	}
}
if(!function_exists('echoExep')){
	/**
	 * Gibt eine Exception aus
	 * @param $e
	 */
	function echoExep($e){
		echo "<pre>";
		echo $e;
		echo "</pre>";
	}
}

if(!function_exists('loadJSON')) {
	/**
	 * Funktion läd einen Json String und wandelt diesen in ein Array um
	 * @param $path
	 * @return mixed
	 */
	function loadJSON($path){
		$file = file_get_contents($path);

		return json_decode($file, true);
	}
}