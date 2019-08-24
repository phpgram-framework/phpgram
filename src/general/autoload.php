<?php
//Klassen
/*
define("LIB",NAPF.DS."src".DS."lib".DS);
define("CONTROLLER",APPPATH."controller".DS);
define('MODEL',APPPATH."model".DS);
*/
require_once (LIB."Autoloader.php");
use Gram\Lib\Autoloader;
//Verzeichnisse gehören in die page.config.php
//Unterverzeichnisse in denen auch nach Klassen gesucht werden soll
/**
 * @deprecated Composer benutzen
 */
define("SUBCLASS",array(
	CONTROLLER."admin".DS,
	CONTROLLER."dev".DS
));

//Unterverzeichnisse für weitere Namespaces
/**
 * @deprecated Composer benutzen
 */
define("NAMESPACES",array(

));

/**
 * @author Jörn Heinemann
 * Erstelle den Klassen Autoloader
 * @deprecated Composer benutzen
 */

$autoload=new Autoloader();

$autoload->registerClassMap(array_merge(array(CONTROLLER,MODEL),SUBCLASS));

$autoload->registerNameSpace(array_merge(array(
	"Gram\Lib"=>LIB,
	"Gram\Lib\auth"=>LIB."auth/",
	"app\model"=>MODEL
),NAMESPACES));

$autoload->register(true);