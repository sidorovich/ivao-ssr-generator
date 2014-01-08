<?php
include 'configuration/config.php';

//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

$data = new IvaoDataReader();

$flights = $data->getFlights();

$departureList = array();
$arrivalList = array();
$transitionList = array();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$cal = isset($_GET['cal']) ? $_GET['cal'] : '';

foreach ($flights as $flight) {

    if (!$flight->hasPlan()) {
        continue;
    }

    if (!$flight->inArea($area[$activeArea])) {
        continue;
    }

    if ( ($action == 'assigne') && ($cal == $flight->callsign) ) {
        $flight->setAssignedTransponder($flight->transponder);
    } elseif (!$flight->getAssignedTransponder() || (($action == 'newsqk') && ($cal == $flight->callsign)) ) {
        $flight = IvaoTransponderService::getInstance()->asigneeToFlight($flight, $location);
    }

    if ($flight->getDepAirportLocation($location) == 'local') {
        $departureList[] = $flight;
    }

    if ($flight->getDestAirportLocation($location) == 'local') {
        $arrivalList[] = $flight;
    }

    if ( ($flight->depAirportLocation != 'local')&& ($flight->destAirportLocation != 'local') ) {
        $transitionList[] = $flight;
    }
}

if ($action != '') {
    header('location: /ssr_generator/');;
    exit;
}
require 'view/header.tpl';
IvaoViewService::getInstance()->render($departureList, $departureFields);
IvaoViewService::getInstance()->render($arrivalList, $arrivalFields);
IvaoViewService::getInstance()->render($transitionList, $transitionFields);
require 'view/footer.tpl';
?>
