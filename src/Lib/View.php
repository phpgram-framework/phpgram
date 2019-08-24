<?php
namespace Gram\Lib;
use \Exception;

/**
 * Class View
 * @package Gram\Lib
 * @author Jörn Heinemann
 * @version 2.0
 * Core class um Templates mit Inhalt zu füllen und sie weiter zu geben
 */
class View
{
    private $template;
    private $cached;

    public function __construct(){} //constructor, da sonst Nameskonflick mit static view

    /**
     * Läd das Template, entweder aus dem Cache oder das normale
     * @param string $template
     */
    private function renderTemp($template){
        $pfad=($this->cached)?TEMPCACHE:TEMPLATES;

        try {
            $file = $pfad . strtolower($template) . '.php';

            if (file_exists($file)) {
                $this->template = $file;
            } else {
                throw new Exception('Template ' . $file . ' not found!');
            }
        }
        catch (Exception $e) {
           echoExep($e);
        }
    }

	/**
	 * Prüft ob bereits ein Cache existiert
	 * @param string $cache
	 * @param int $id
	 * Welcher Cache soll geladen werden
	 * @param bool $exp
	 * Unterscheidet zwischen Sprachen
	 * oder alles prüfen
	 * @return bool
	 */
    public static function cacheExists($cache,$id,$exp=true){
		$cache.="id".$id;

        if($exp){
        	//Unterscheidet zwischen Sprachen
        	$cache.="l".Lang::lang()->islang();
			$file = TEMPCACHE . strtolower($cache) . '.php';
			return file_exists($file);
		}

		//Alle cache die zu der Id passen
		$files = glob(TEMPCACHE . strtolower($cache) . '.php');

		foreach ($files as $file) {
			if (file_exists($file)) {
				return true;
			}
		}

		return false;
    }

	/**
	 * Löscht den Cache. Wird benötigt wenn Template aktualisiert wurde (durch Insert, Update, Delete)
	 * @param string $template
	 * @param int $id
	 * Welcher Cache gelöscht werden soll
	 */
    public static function deleteCache($template,$id){
        $files = glob(TEMPCACHE . strtolower($template."id".$id."l*") . '.php');

		foreach ($files as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
        }
    }

    /**
     * Normaler function call: View::view(template,array('foo' => 'bar')); im Template kann dann auf $foo zugegriffen werden.
     * hier wird das Template included und die Varaiblen mit Inhalt gefüllt.
     * Das fertige Template wird zurückgegeben (Template und Werte werden per Outputbuffer gesammelt)
     * Zusätzlich kann auch ein Cache des Templates erstellt werden:
     * $cache = false -> hier wird nie ein Cache erstellt benötigt da wenn kein Cache gefunden (cached=false) immer ein Cache erstellt werden kann (siehe 2. if).
     * $cached -> abfrage ob es bereits einen Cache gibt (muss vor dem Aufruf von view geprüft werden)
     * $cached = true -> keine Varaiblen extraieren. Das gecachte Template wird geladen
     * ""  ""  = false und $cache = true -> erstelle neue File im Cacheordner mit der jeweiligen id ($id) und dem namen des Templates
     *                                   -> File erhält den Inhalt der sonst ausgegeben wird.
     * zum Schluss: gebe das gecachete Template oder das erstellt Template zurück
     * @param string $template
     * @param array $variables
	 * Varaiblen die als Array übergeben werden und auf die das Tempalte dann zugriff hat
     * @param bool $cache
	 * Soll gecacht werden
     * @param bool $cached
	 * Existert bereits ein Cache, wenn ja den Cache laden
     * @param bool|int $id
	 * die Id des Cache
     * @return string
	 * Gibt das Template mit den übergeben Varaiblen zurück
     */
    public static function view($template,$variables = array(),$cache=false,$cached=false,$id=false) {
        $view=new View();
		$view->cached=$cached;

        //hole cache datei
        if($cache && $cached && $id!=false){
			$template.="id".$id."l".Lang::lang()->islang();
		}

        $view->renderTemp($template);
        
        if(!$cached){
            extract($variables);
            debug_console("Variablen extrct");
        }

        //startet den Output Buffer: bis zu clean() wird alles gesammelt und nicht ausgegeben
        ob_start();
        require $view->template;
        $renderedView = ob_get_clean(); //übergibt alles was im Outputbuffer gesammelt wurde und beendet ihn.
        
        //erstelle Cache nur wenn die Seite gecached werden soll und sie es noch nicht ist
        if($cache && !$cached && $id!=false){
            $file=TEMPCACHE. strtolower($template."id".$id."l".Lang::lang()->islang()) . '.php';
			$dirname = dirname($file);

			//erstelle neuen ordner
			if(!is_dir($dirname)){
				mkdir($dirname,0777,true);
			}

        	$fh=fopen($file, 'w');
            fwrite($fh,$renderedView);
            fclose($fh);

            debug_console("Cache erstellt für: ". $file);
        }
        
        return $renderedView;
    }
}