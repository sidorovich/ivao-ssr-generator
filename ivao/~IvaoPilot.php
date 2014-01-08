<?php
include_once 'IvaoClient.php';

class IvaoPilot extends IvaoClient
{




    var $callsign;
    var $vid;
    var $realname;
    var $latitude;
    var $longitude;
    var $altitude;
    var $groundSpeed;
    var $plannedData = array('aircraft' => null,
                             'tascruise' => null,
                             'depAirport' => null,
                             'altitude' => null,
                             'destAirport' => null,
                             'revision' => null,
                             'flighttype' => null,
                             'deptime' => null,
                             'actdeptime' => null,
                             'hrsenroute' => null,
                             'minenroute' => null,
                             'hrsfuel' => null,
                             'minfuel' => null,
                             'altAirport' => null,
                             'remarks' => null,
                             'route' => null,
                             'depAirportLat' => null,
                             'depAirportLon' => null,
                             'destAirportLat' => null,
                             'destAirportLon' => null,
                             'altAirport2' => null,
                             'typeOfFlight' => null,
                             'pob' => null);

    var $server;
    var $protRevision;
    var $rating;
    var $transponder;

    var $timeConnected;
    var $clientSoftwareName;
    var $clientSoftwareVersion;
    var $adminRating;
    var $atcOrPilotRating;

    var $trueHeading;
    var $onGround;

    var $assignedTransponder;

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

        $this->plannedData = array('conunt'             => $count,
                                   'aircraftType'       => $actType,
                                   'aircraftCategory'   => $cat,
                                   'aircraftEquipments' => $equipments,

                                   'aircraft'       => $data['planned_aircraft'],
                                   'tascruise'      => $data['planned_tascruise'],
                                   'depAirport'     => $data['planned_depairport'],
                                   'altitude'       => $data['planned_altitude'],
                                   'destAirport'    => $data['planned_destairport'],
                                   'revision'       => $data['planned_revision'],
                                   'flighttype'     => $data['planned_flighttype'],
                                   'deptime'        => $data['planned_deptime'],
                                   'actdeptime'     => $data['planned_actdeptime'],
                                   'hrsenroute'     => $data['planned_hrsenroute'],
                                   'minenroute'     => $data['planned_minenroute'],
                                   'hrsfuel'        => $data['planned_hrsfuel'],
                                   'minfuel'        => $data['planned_minfuel'],
                                   'altAirport'     => $data['planned_altairport'],
                                   'remarks'        => $data['planned_remarks'],
                                   'route'          => $data['planned_route'],
                                   'depAirportLat'  => $data['planned_depairport_lat'],
                                   'depAirportLon'  => $data['planned_depairport_lon'],
                                   'destAirportLat' => $data['planned_destairport_lat'],
                                   'destAirportLon' => $data['planned_destairport_lon'],
                                   'altAirport2'    => $data['planned_altairport2'],
                                   'typeOfFlight'   => $data['planned_typeofflight'],
                                   'pob'            => $data['planned_pob']);

        $this->server                = $data['server'];
        $this->protRevision          = $data['protrevision'];
        $this->rating                = $data['rating'];
        $this->transponder           = $data['transponder'];
        $this->timeConnected         = $data['time_connected'];
        $this->clientSoftwareName    = $data['client_software_name'];
        $this->clientSoftwareVersion = $data['client_software_version'];
        $this->adminRating           = $data['adminrating'];
        $this->atcOrPilotRating      = $data['atc_or_pilotrating'];
        $this->trueHeading           = $data['true_heading'];
        $this->onGround              = $data['onground'];
    }

    function inArea($area)
    {
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
        if (!empty($this->plannedData['depAirport']) && !empty($this->plannedData['destAirport'])) {
            return true;
        } else {
            return false;
        }
    }
}
?>