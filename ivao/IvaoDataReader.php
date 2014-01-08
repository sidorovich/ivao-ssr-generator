<?php
include_once 'IvaoFlight.php';
include_once 'IvaoAtc.php';
include_once 'IvaoServer.php';

class IvaoDataReader
{
    var $dataFolder;

    var $statusFile;
    var $statusUrl;
    var $statusTerminateTime;

    var $wazzupFile;
    var $wazzupUrl;
    var $wazzupGzUrl;
    var $wazzupTerminateTime;

    var $flights = array();
    var $atc = array();
    var $servers = array();

    function __construct($config = array())
    {
        $this->dataFolder = isset($config['dataFolder'])? $config['dataFolder'] :'data/';


        $this->statusFile = isset($config['statusFile']) ? $config['statusFile'] : $this->dataFolder . 'status.txt';

        $this->statusUrl = isset($config['statusUrl']) ? $config['statusUrl'] :'http://www.ivao.aero/whazzup/status.txt';
        $this->statusTerminateTime = isset($config['statusTerminateTime']) ? $config['statusTerminateTime'] : 60*60*24;

        $this->wazzupFile = isset($config['wazzupFile']) ? $config['wazzupFile'] :$this->dataFolder . 'wazzup.txt';
        $this->wazzupTerminateTime = isset($config['wazzupTerminateTime']) ? $config['wazzupTerminateTime'] : 60;

        $this->loadStatus();

        $this->getWazzup();
    }

    private function loadStatus()
    {
        $config = array();

        if (file_exists($this->statusFile)&&(time() - filemtime($this->statusFile)) < $this->statusTerminateTime) {
            $status = file_get_contents($this->statusFile);
        } else {
            $status = file_get_contents($this->statusUrl);
            file_put_contents($this->statusFile, $status);
        }

        foreach (explode("\r\n", $status) as $line) {
            $tLine = trim($line);
            if ((strlen($tLine) > 0) && ($tLine{0} == ';')) {
                continue;
            }
            @list($var, $param) = explode("=", $tLine);

            if ($param != null) {
                if (isset($config[$var])) {
                    if (!is_array($config[$var])) {
                        $param0 = $config[$var];
                        $config[$var] = array();
                        $config[$var][] = $param0;
                    }
                    $config[$var][] = $param;
                } else {
                    $config[$var] = $param;
                }
            }
        }


        $this->wazzupUrl = is_array($config['url0']) ? $config['url0'][0] : $config['url0'];
    }

    function getWazzup()
    {
        $wazzup = array();

        if (file_exists($this->wazzupFile)&&(time() - filemtime($this->wazzupFile)) < $this->wazzupTerminateTime) {
            $contents = file_get_contents($this->wazzupFile);
        } else {
            $contents = file_get_contents($this->wazzupUrl);
            file_put_contents($this->wazzupFile, $contents);
        }

        $block = "!GENERAL";

        foreach (explode("\n", $contents) as $line) {
            switch ($line) {
                case "":
                    break;

                case "!GENERAL":
                    $block = 'general';
                    break;

                case "!CLIENTS":
                    $block = 'clients';
                    break;

                case "!SERVERS":
                    $block = 'servers';
                    break;

                default:
                    switch ($block) {
                        case 'general':
                            list($key, $value) = explode("=", $line);
                            $wazzup[$block][trim($key)] = trim($value);
                            break;

                        case 'clients':
                            $clientData = array_combine(IvaoClient::$FIELDS, explode(":", $line));

                            switch ($clientData['clienttype']) {
                                case 'ATC':
                                    $client = new IvaoAtc($clientData);
                                    break;

                                case 'PILOT':
                                    $client = new IvaoFlight($clientData);
                                    break;

                                case 'FOLME':
                                    break;

                                default:
                                    break;
                            }

                            $wazzup[$block][$clientData['clienttype']][] = $client;
                            break;

                        case 'servers':
                            $serverData = array_combine(IvaoServer::$FIELDS, explode(":", $line));
                            $server = new IvaoServer($serverData);
                            $wazzup[$block][] = $server;
                            break;

                        default:
                            break;
                    }
                    break;
            }
        }

        $this->flights = $wazzup['clients']['PILOT'];
        $this->atc = $wazzup['clients']['ATC'];
        $this->servers = $wazzup['servers'];

        return $wazzup;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    function getFlights()
    {
        return $this->flights;
    }


    function getAtc()
    {
        return $this->atc;
    }

    function getServers()
    {
        return $this->servers;
    }
}
?>