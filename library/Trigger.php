<?php

final class Trigger
{
    private $debugMode;
    private $arduinoURL;
    private $cURLOptions = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_USERAGENT      => "TriggerEvent",
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_MAXREDIRS      => 2,
    );


    function __construct($debugMode = false) {
        $this->debugMode = $debugMode;
        if($this->debugMode) ob_implicit_flush();
        $this->debug("Util::getInstance -> Load IP \n");
        $this->arduinoURL = \StoredLibrary\Util::triggerLoadIP();
        $this->debug("Util::getInstance -> IP is $this->arduinoURL \n");
    }

    public function start() {
        while(true) {
            $this->debug("file exists events.json ? \n");
            if(file_exists("..\\triggers\\events.json")) {
                $this->debug("load file events.json \n");
                $eventsList = json_decode(file_get_contents("..\\triggers\\events.json"));
                $this->debug("events.json size = ". sizeof($eventsList). "\n");
                if(!empty($eventsList)) {
                    $hourNow = date("H:i");
                    $this->debug("time now $hourNow ...\n");
                    $this->debug("start iterating events... \n");
                    foreach($eventsList as $event) {
                        $this->debug("start iteration idEvent= $event->idEvent \n");
                        $this->debug("event - $event->dispEvent  action - $event->actionExecute  time current event - $event->timeExecution \n");
                        $this->debug("checkPresence - $event->usesCheckPresence   averageOn - $event->usesAverageConsumption  qtd - $event->averageConsumption \n");
                        if(strtotime($hourNow) == strtotime($event->timeExecution)) {
                            $this->debug("start action event \n");
                            $this->requestArduino("?$event->dispEvent=$event->actionExecute");
                            $this->debug("finish action event \n");
                        }
                    }
                    $this->debug("finish iterating events \n");
                }
            } else {
                $this->debug("events.json not exists in folder.\n");
            }
            sleep(10);
        }
    }

    private function requestArduino($disp) {
        $actionCurl      = curl_init( $this->arduinoURL . $disp);
        curl_setopt_array( $actionCurl, $this->cURLOptions );
        curl_exec( $actionCurl );
        curl_close( $actionCurl );
    }

    private function debug($line) {
        if($this->debugMode) echo $line;
    }

}

error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');
define('APP_LIBRARY', realpath(__DIR__ . DIRECTORY_SEPARATOR . 'library'));
require_once(APP_LIBRARY . DIRECTORY_SEPARATOR . 'Classloader.php');
$loader = new \StoredLibrary\Classloader(APP_LIBRARY);
$loader->registerNamespace('StoredLibrary', __DIR__);
$loader->register();

define('DEBUG', TRUE);
$trigger = new Trigger(DEBUG);
$trigger->start();
