<?php
class DB {

    protected static $_instance;
    protected $_resource;

    private function __construct($server, $database, $username, $password, $prefix = '')
    {
        $this->_resource = mysql_connect($server, $username, $password) or die("Could not connect: " . mysql_error());
        mysql_select_db($database, $this->_resource);
        $this->prefix = $prefix;
    }

    /**
     * Enter description here...
     * @param string $server
     * @param string $database
     * @param string $username
     * @param string $password
     *
     * @return DB
     */
    static function getInstance($server = null, $database = null, $username = null, $password = null)
    {
        if (self::$_instance == null) {
            self::connect($server, $database, $username, $password);
        }
        return self::$_instance;
    }

    /**
     * Enter description here...
     *
     * @param string $server
     * @param string $database
     * @param string $username
     * @param string $password
     */
    static function connect($server, $database, $username, $password)
    {
        self::$_instance = new self($server, $database, $username, $password);
    }

    public function getSSRCode($callsign)
    {
        $sql = "SELECT * FROM `{$this->prefix}ssr_assigned_codes` WHERE `callsign` = '{$callsign}' AND `datetime` >= DATE_SUB(NOW(), INTERVAL 4 HOUR)";
        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);

            $sql = "UPDATE `{$this->prefix}ssr_assigned_codes` SET `datetime` = NOW() WHERE `id` = {$row['id']}";
            mysql_query($sql);

            return $row['assignedCode'];

        } else {

            return false;

        }
    }

    public function setSSRCode($callsign, $code)
    {
        $sql = "SELECT * FROM `{$this->prefix}ssr_assigned_codes` WHERE `callsign` = '{$callsign}' AND `datetime` >= DATE_SUB(NOW(), INTERVAL 4 HOUR)";
        $result = mysql_query($sql);

        if (mysql_num_rows($result) > 0) {
            $sql = "UPDATE `{$this->prefix}ssr_assigned_codes` SET `assignedCode` = '{$code}', `datetime` = NOW() WHERE `callsign` = '{$callsign}'";
        } else {
            $sql = "INSERT INTO `{$this->prefix}ssr_assigned_codes` SET `callsign` = '{$callsign}', `assignedCode` = '{$code}', `datetime` = NOW()";
        }

        return mysql_query($sql);
    }

    public function getNextCode($type)
    {

        $sql = "SELECT * FROM `{$this->prefix}ssr_codes` WHERE `name` = '{$type}'";
        $result = mysql_query($sql);

        if (mysql_num_rows($result) == 0)
        {
            return false;
        }

        $sql = "SELECT * FROM `{$this->prefix}ssr_codes` WHERE `name` = '{$type}' AND `flag` = 0";

        $result = mysql_query($sql);
        sleep(0.5);
        while (mysql_num_rows($result) == 0) {
            sleep(0.5);
            $result = mysql_query($sql);
        }

        $sql = "UPDATE `{$this->prefix}ssr_codes` SET `flag` = 1 WHERE `name` = '{$type}'";
        mysql_query($sql);

        $row = mysql_fetch_array($result);
        return $row['value'];
    }

    public function setNextCode($type, $code)
    {
        $sql = "UPDATE `{$this->prefix}ssr_codes` SET `flag` = 0, `value` = '{$code}' WHERE `name` = '{$type}'";
        mysql_query($sql);
    }
}
?>