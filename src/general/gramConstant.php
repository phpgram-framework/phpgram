<?php
/**
 * @author Jörn Heinemann
 * Konstante Pfade für das Framework
 */
const DS = DIRECTORY_SEPARATOR;

//Allgemeine Ordner Diese nicht ändern!
define("APPPATH",ROOTPATH.DS."app".DS);
define("GRAMCONFIG",ROOTPATH.DS."config".DS);
define("RESOURCES",ROOTPATH.DS."resources".DS);

//Templates
define("VIEWS",APPPATH."views".DS);
define("TEMPLATES",VIEWS."templates".DS);
define("TEMPCACHE",VIEWS."cache".DS);

//Userdata
define("STORAGE",ROOTPATH.DS."storage".DS);

//Controller Namespace
define("CNAMESPACE","App\Http\Controller\\");
//Middleware Namespace
define("MNAMESPACE","App\Http\Middleware\\");

//Routing
//Regex Chunk
define("REGEXCHUNK",10);