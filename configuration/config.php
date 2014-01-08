<?php
###################### includes #######################
// reader
include_once 'ivao/IvaoDataReader.php';
// services
include_once 'ivao/IvaoTransponderService.php';
include_once 'ivao/IvaoViewService.php';
// database
include_once 'database/Db.php';

#################### Reader config ####################

$readerConfig = array('dataFolder'          => 'data/',
                      'statusFile'          => 'data/status.txt',
                      'statusUrl'           => 'http://www.ivao.aero/whazzup/status.txt',
                      'statusTerminateTime' => 60*60*24,
                      'wazzupFile'          => 'data/wazzup.txt',
                      'wazzupTerminateTime' => 60);

############### Time synchronize config ###############

$UtcTimeVar = -3;

################### Database config ###################

$dbHost = 'localhost';
$dbName = 'db_name';
$dbUser = 'db_user';
$dbPassword = 'db_password';
$prefix = '';

################### Area config ########################

$area['UMMV'] = array(array('latitude' => 54.04, 'longitude' => 22.55),
                      array('latitude' => 56.46, 'longitude' => 26.94),
                      array('latitude' => 56.10, 'longitude' => 31.48),
                      array('latitude' => 53.41, 'longitude' => 33.33),
                      array('latitude' => 52.08, 'longitude' => 32.23),
                      array('latitude' => 51.25, 'longitude' => 30.50),
                      array('latitude' => 51.19, 'longitude' => 22.84));

$area['TEST'] = array(array('latitude' => 50.41, 'longitude' => 21.50),
                      array('latitude' => 48.68, 'longitude' => 23.89),
                      array('latitude' => 48.56, 'longitude' => 19.64));

$area['EHAA'] = array(array('latitude' => 51.52, 'longitude' => 02.00),
                      array('latitude' => 55.02, 'longitude' => 04.99),
                      array('latitude' => 54.99, 'longitude' => 06.46),
                      array('latitude' => 50.60, 'longitude' => 07.12));

$activeArea = 'UMMV';

################### Location config ####################

$location = array('local' => array(1 => array(),
                                   2 => array('UM'),
                                   3 => array(),
                                   4 => array()),

                  'west'  => array(1 => array('C', 'D', 'K', 'B', 'P', 'M', 'T', 'S', 'G', 'P', 'E', 'L'),
                                   2 => array(),
                                   3 => array(),
                                   4 => array('UMKK')),

                  'east'  => array(1 => array('H', 'F', 'O', 'V', 'Z', 'U', 'W', 'Y', 'A', 'R', 'N'),
                                   2 => array('LT', 'LC', 'LL'),
                                   3 => array(),
                                   4 => array()));

##################### View config #####################
//Fields
$departureAirport   = array('title'   => 'From',
                            'pattern' => '%depAirport%',
                            'style'   => 'dep-apt');

$destinationAirport = array('title'   => 'To',
                            'pattern' => '%destAirport%',
                            'style'   => 'desp-apt');

$plannedAltitude    = array('title'   => 'Alt',
                            'pattern' => '%plannedAltitude%',
                            'style'   => 'altitude');

$flightRule         = array('title'   => 'Rule',
                            'pattern' => '%flighttype%',
                            'style'   => 'flight-rule');

$arrivalTime        = array('title'   => 'STA<br/>{now}',
                            'pattern' => '%hrsArrival%:%minArriaval%',
                            'style'   => 'arr-time');

$departureTime      = array('title'   => 'EOBT<br/>{now}',
                            'pattern' => '%hrsDeparture%:%minDeparture%',
                            'style'   => 'dep-time');

$aircraftType       = array('title'   => 'A/C',
                            'pattern' => '%aircraftType%',
                            'style'   => 'act-type');

$route              = array('title'   => 'Route',
                            'pattern' => '%route%',
                            'style'   => 'route');

$squawk             = array('title'   => 'Squawk<br/><span>now/assign</span>',
                            'pattern' => '<a href="?action=assigne&cal=%callsign%">%transponder%</a> /'
                                       . ' <a href="?action=newsqk&cal=%callsign%">%assignedTransponder%</a>',
                            'style'   => 'squawk');

$arrivalTitle       = array('title'   => 'Arrival',
                            'pattern' => '%callsign%',
                            'style'   => 'title');

$departureTitle     = array('title'   => 'Depart',
                            'pattern' => '%callsign%',
                            'style'   => 'title');

$transitionTitle    = array('title'   => 'Transit',
                            'pattern' => '%callsign%',
                            'style'   => 'title');
//Rows
$arrivalFields = array($arrivalTitle, $departureAirport, $destinationAirport, $plannedAltitude, $flightRule,
                       $arrivalTime, $aircraftType, $route, $squawk);

$departureFields = array($departureTitle, $departureAirport, $destinationAirport, $plannedAltitude, $flightRule,
                         $departureTime, $aircraftType, $route, $squawk);

$transitionFields = array($transitionTitle, $departureAirport, $destinationAirport, $plannedAltitude, $flightRule,
                          $aircraftType, $route, $squawk);

######################### Init #########################

$db = DB::getInstance($dbHost, $dbName, $dbUser, $dbPassword, $prefix);
