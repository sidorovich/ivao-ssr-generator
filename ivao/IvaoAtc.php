<?php
include_once 'IvaoClient.php';

class IvaoAtc extends IvaoClient
{
    var $callsign;
    var $vid;
    var $realname;
    var $frequency;
    var $latitude;
    var $longitude;
    var $altitude;

    var $server;
    var $protRevision;
    var $rating;
    var $facilitytype;
    var $visualrange;

    var $atis_message;
    var $time_last_atis_received;
    var $timeConnected;
    var $clientSoftwareName;
    var $clientSoftwareVersion;
    var $adminRating;
    var $atcOrPilotRating;

    function __construct($data)
    {

        $this->callsign = $data['callsign'];
        $this->vid = $data['vid'];
        $this->realname = $data['realname'];
        $this->frequency = $data['frequency'];
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
        $this->altitude = $data['altitude'];

        $this->server = $data['server'];
        $this->protRevision = $data['protrevision'];
        $this->rating = $data['rating'];
        $this->facilitytype = $data['facilitytype'];
        $this->visualrange = $data['visualrange'];

        $this->atis_message = $data['atis_message'];
        $this->time_last_atis_received = $data['time_last_atis_received'];
        $this->timeConnected = $data['time_connected'];
        $this->clientSoftwareName = $data['client_software_name'];
        $this->clientSoftwareVersion = $data['client_software_version'];
        $this->adminRating = $data['adminrating'];
        $this->atcOrPilotRating = $data['atc_or_pilotrating'];

    }

}
?>