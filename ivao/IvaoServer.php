<?php
class IvaoServer {
    static $FIELDS = array('ident', 'hostname_or_IP', 'location', 'name',
                           'clients_connection_allowed', 'max_connections');

    var $ident;
    var $host;
    var $location;
    var $name;
    var $clientsConnectionAllowed;
    var $maxConnections;

    function __construct($data)
    {
        $this->ident                    = $data['ident'];
        $this->host                     = $data['hostname_or_IP'];
        $this->location                 = $data['location'];
        $this->name                     = $data['name'];
        $this->clientsConnectionAllowed = $data['clients_connection_allowed'] == 1 ? true : false;
        $this->maxConnections           = $data['max_connections'];
    }
}
?>