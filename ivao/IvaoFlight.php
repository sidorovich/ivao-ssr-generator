<?php
include_once 'IvaoClient.php';

class IvaoFlight extends IvaoClient
{
// TODO: Разделить данные на подклассы по смыслу.

    //Flight
    var $callsign;
    var $actdeptime;
    var $transponder;
    var $assignedTransponder;
    var $timeConnected;

    var $trueHeading;
    var $onGround;

    //Pilot
    var $vid;
    var $realname;
    var $rating;
    var $adminRating;
    var $atcOrPilotRating;

    //Aircraft
    var $latitude;
    var $longitude;
    var $altitude;
    var $groundSpeed;

    var $aircraftType;
    var $aircraftCategory;
    var $aircraftEquipments;


    //FlightPlan
    var $revision;
    var $count;
    var $aircraft; //it's aircraft
    var $tascruise;
    var $plannedAltitude;
    var $flighttype;
    var $deptime;

    var $depAirport;
    var $depAirportLat;
    var $depAirportLon;

    var $depAirportLocation;

    var $destAirport;
    var $destAirportLat;
    var $destAirportLon;

    var $destAirportLocation;

    var $hrsenroute;
    var $minenroute;
    var $hrsfuel;
    var $minfuel;

    var $hrsArrival; // custom
    var $minArriaval; // custom
    var $hrsDeparture; // custom
    var $minDeparture; //custom

    var $altAirport;
    var $altAirport2;
    var $remarks;
    var $route;
    var $typeOfFlight;
    var $pob;

    //Connect
    var $server;
    var $protRevision;
    var $clientSoftwareName;
    var $clientSoftwareVersion;

    function __construct($data)
    {
        $count          = null;
        $actType        = null;
        $cat_equipments = null;
        $cat            = null;
        $equipments     = null;

        if (!empty($data['planned_aircraft'])) {
            list($count, $actType, $cat_equipments) = explode("/", $data['planned_aircraft']);
            list($cat, $equipments) = explode ("-", $cat_equipments);
        }

        $this->callsign    = $data['callsign'];
        $this->vid         = $data['vid'];
        $this->realname    = $data['realname'];
        $this->latitude    = $data['latitude'];
        $this->longitude   = $data['longitude'];
        $this->altitude    = $data['altitude'];
        $this->groundSpeed = $data['groundspeed'];

        $this->count              = $count;
        $this->aircraftType       = $actType;
        $this->aircraftCategory   = $cat;
        $this->aircraftEquipments = $equipments;

        $this->aircraft        = $data['planned_aircraft'];
        $this->tascruise       = $data['planned_tascruise'];
        $this->plannedAltitude = $data['planned_altitude'];

        $this->depAirport     = $data['planned_depairport'];
        $this->depAirportLat  = $data['planned_depairport_lat'];
        $this->depAirportLon  = $data['planned_depairport_lon'];

        $this->destAirport    = $data['planned_destairport'];
        $this->destAirportLat = $data['planned_destairport_lat'];
        $this->destAirportLon = $data['planned_destairport_lon'];

        $this->revision     = $data['planned_revision'];
        $this->flighttype   = $data['planned_flighttype'];
        $this->deptime      = str_pad($data['planned_deptime'], 4, '0', STR_PAD_LEFT);
        $this->actdeptime   = str_pad($data['planned_actdeptime'], 4, '0', STR_PAD_LEFT);
        $this->hrsenroute   = $data['planned_hrsenroute'];
        $this->minenroute   = $data['planned_minenroute'];
        $this->hrsfuel      = $data['planned_hrsfuel'];
        $this->minfuel      = $data['planned_minfuel'];
        $this->altAirport   = $data['planned_altairport'];
        $this->altAirport2  = $data['planned_altairport2'];
        $this->remarks      = $data['planned_remarks'];
        $this->route        = $data['planned_route'];
        $this->typeOfFlight = $data['planned_typeofflight'];
        $this->pob          = $data['planned_pob'];

        $this->server                = $data['server'];
        $this->protRevision          = $data['protrevision'];
        $this->rating                = $data['rating'];
        $this->transponder           = str_pad($data['transponder'], 4, '0', STR_PAD_LEFT);
        $this->timeConnected         = $data['time_connected'];
        $this->clientSoftwareName    = $data['client_software_name'];
        $this->clientSoftwareVersion = $data['client_software_version'];
        $this->adminRating           = $data['adminrating'];
        $this->atcOrPilotRating      = $data['atc_or_pilotrating'];
        $this->trueHeading           = $data['true_heading'];
        $this->onGround              = $data['onground'];

        //cusom time operation
        $this->minDeparture = substr($this->deptime, -2);
        $this->hrsDeparture = substr($this->deptime, -4, 2);
//        var_dump($this->callsign, $this->deptime, $this->hrsDeparture, $this->minDeparture);

        $arrTime = mktime($this->hrsDeparture + $this->hrsenroute, $this->minDeparture + $this->minenroute);

        $this->hrsArrival = date('H', $arrTime);
        $this->minArriaval = date('i', $arrTime);

    }

    function inArea($area)
    {
//        foreach ($areas as $area) {
            $point = end($area);
            $sign = 0;

            foreach ($area as $nextPoint) {
                $x = $this->latitude;
                $y = $this->longitude;

                $x1 = $point['latitude'];
                $y1 = $point['longitude'];

                $x2 = $nextPoint['latitude'];
                $y2 = $nextPoint['longitude'];

                $newSign = ($x - $x1) * ($y1 - $y2) - ($y - $y1) * ($x1 - $x2);

                if ($sign == 0) {
                    $sign = $newSign;
                }

                if ($sign * $newSign < 0) {
                    return false;
                }

                $point = $nextPoint;
            }
//        }

        return true;
    }

    /**
     * Get assigned ssr code for this pilot
     *
     * @return string | bool(false)
     */
    function getAssignedTransponder()
    {
        $db = DB::getInstance();
        $this->assignedTransponder = $db->getSSRCode($this->callsign);
        return $this->assignedTransponder;
    }

    function setAssignedTransponder($code)
    {
        $this->assignedTransponder = $code;

        $db = DB::getInstance();
        $db->setSSRCode($this->callsign, $code);
    }

    function hasPlan()
    {
        if (!empty($this->depAirport) && !empty($this->destAirport)) {
            return true;
        } else {
            return false;
        }
    }

    function isVfr()
    {
         return $flight->flighttype == 'V';
    }

    private function _getLocation($needle, $pattern)
    {
        for ($i = 4; $i >= 1; $i--)
        {
            foreach ($pattern as $name => $variant) {
                foreach ($variant[$i] as $area) {
                    if (preg_match("/^{$area}/", $needle)) {
                        return $name;
                    }
                }
            }
        }
    }

    function getDestAirportLocation($pattern)
    {
        if (isset($this->destAirportLocation)) {
            return $this->destAirportLocation;
        }

        $this->destAirportLocation = $this->_getLocation($this->destAirport, $pattern);
        return $this->destAirportLocation;
    }

    function getDepAirportLocation($pattern)
    {
        if (isset($this->depAirportLocation)) {
            return $this->depAirportLocation;
        }

        $this->depAirportLocation = $this->_getLocation($this->depAirport, $pattern);
        return $this->depAirportLocation;
    }
}
?>