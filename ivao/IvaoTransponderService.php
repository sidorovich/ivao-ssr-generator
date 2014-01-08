<?php

class IvaoTransponderService
{
    protected static $_instance;

    /**
     * 0000-7777
     */
    const TYPE_ALL = 'ALL';
    /**
     * 1000, 1100, 3000, 4000, 5000, 6000
     *
     */
    const TYPE_NATO = 'NATO';

    /**
     * 0030-0037
     *
     */
    const TYPE_CALIBRATION = 'CALIBRATION';

    /**
     * 0100-0177
     *
     */
    const TYPE_ROYAL = 'ROYAL';

    /**
     * 2001-2777
     */
    const TYPE_VFR = 'VFR';

    /**
     * 1001-1077
     */
    const TYPE_IFR_LOCAL = 'IFR_LOCAL';

    /**
     * 4001-4777
     *
     */
    const TYPE_IFR_WEST_DEPARTURE = 'IFR_WEST_DEPARTURE';

    /**
     * 6001-6377
     */
    const TYPE_IFR_WEST_ARRIVAL   = 'IFR_WEST_ARRIVAL';

    /**
     * 6401-6777
     */
    const TYPE_IFR_WEST_TRANSIT   = 'IFR_WEST_TRANSIT';

    /**
     * 3001-3777
     */
    const TYPE_IFR_EAST_DEPARTURE = 'IFR_EAST_DEPARTURE';

    /**
     * 5001-5377
     */
    const TYPE_IFR_EAST_ARRIVAL   = 'IFR_EAST_ARRIVAL';

    /**
     * 5401-5777
     */
    const TYPE_IFR_EAST_TRANSIT   = 'IFR_EAST_TRANSIT';

    private $transponderRanges = array(IvaoTransponderService::TYPE_ALL                => array('begin' => '0000', 'end' => '7777'),
                                       IvaoTransponderService::TYPE_CALIBRATION        => array('begin' => '0030', 'end' => '0037'),
                                       IvaoTransponderService::TYPE_ROYAL              => array('begin' => '0100', 'end' => '0177'),
                                       IvaoTransponderService::TYPE_VFR                => array('begin' => '2001', 'end' => '2777'),
                                       IvaoTransponderService::TYPE_IFR_LOCAL          => array('begin' => '1001', 'end' => '1077'),
                                       IvaoTransponderService::TYPE_IFR_WEST_DEPARTURE => array('begin' => '4001', 'end' => '4777'),
                                       IvaoTransponderService::TYPE_IFR_WEST_ARRIVAL   => array('begin' => '6001', 'end' => '6377'),
                                       IvaoTransponderService::TYPE_IFR_WEST_TRANSIT   => array('begin' => '6401', 'end' => '6777'),
                                       IvaoTransponderService::TYPE_IFR_EAST_DEPARTURE => array('begin' => '3001', 'end' => '3777'),
                                       IvaoTransponderService::TYPE_IFR_EAST_ARRIVAL   => array('begin' => '5001', 'end' => '5377'),
                                       IvaoTransponderService::TYPE_IFR_EAST_TRANSIT   => array('begin' => '5401', 'end' => '5777'),
                                       IvaoTransponderService::TYPE_NATO               => array('1000', '1100', '3000', '4000', '5000', '6000'));

    private function __construct()
    {}
    /**
     * @return IvaoTransponderService
     */
    static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function asigneeToFlight(IvaoFlight $flight, $location)
    {
        if ($flight->flighttype == 'V') {
            $type = IvaoTransponderService::TYPE_VFR;
        } else{
            $depAirport  = $flight->getDepAirportLocation($location);
            $destAirport = $flight->getDestAirportLocation($location);

            if ( ( $depAirport == 'local') && ( $destAirport == 'local') ) {
                $type = IvaoTransponderService::TYPE_IFR_LOCAL;
            } elseif ( ( $depAirport == 'local') && ( $destAirport == 'west') ) {
                $type = IvaoTransponderService::TYPE_IFR_WEST_DEPARTURE;
            } elseif ( ( $depAirport == 'local') && ( $destAirport == 'east') ) {
                $type = IvaoTransponderService::TYPE_IFR_EAST_DEPARTURE;
            } elseif ( ( $depAirport == 'west') && ( $destAirport == 'local') ) {
                $type = IvaoTransponderService::TYPE_IFR_WEST_ARRIVAL;
            } elseif ( ( $depAirport == 'east') && ( $destAirport == 'local') ) {
                $type = IvaoTransponderService::TYPE_IFR_EAST_ARRIVAL;
            } elseif ( ( $depAirport == 'east') && ( $destAirport != 'local') ) {
                $type = IvaoTransponderService::TYPE_IFR_WEST_TRANSIT;
            } elseif ( ( $depAirport == 'west') && ( $destAirport != 'local') ) {
                $type = IvaoTransponderService::TYPE_IFR_EAST_TRANSIT;
            }
        }

        $code = $this->_getNextTransponder($type);

        $flight->setAssignedTransponder($code);

        return $flight;
    }

    private function _getNextTransponder($type)
    {
        $code = DB::getInstance()->getNextCode($type);
        $newCode = $this->_getNewCode($code, $type);
        DB::getInstance()->setNextCode($type, $newCode);
        return $code;
    }

    private function _getNewCode($oldCode, $type)
    {
        switch ($type) {
            case IvaoTransponderService::TYPE_ALL :
            case IvaoTransponderService::TYPE_CALIBRATION :
            case IvaoTransponderService::TYPE_ROYAL :
            case IvaoTransponderService::TYPE_VFR :
            case IvaoTransponderService::TYPE_IFR_LOCAL :
            case IvaoTransponderService::TYPE_IFR_EAST_DEPARTURE :
            case IvaoTransponderService::TYPE_IFR_EAST_ARRIVAL :
            case IvaoTransponderService::TYPE_IFR_EAST_TRANSIT :
            case IvaoTransponderService::TYPE_IFR_WEST_DEPARTURE :
            case IvaoTransponderService::TYPE_IFR_WEST_ARRIVAL :
            case IvaoTransponderService::TYPE_IFR_WEST_TRANSIT :

                $decCode = $this->_dec($oldCode);

                $decCode++;

                if ($decCode > $this->_dec($this->transponderRanges[$type]['end'])) {
                    $newCode = $this->transponderRanges[$type]['begin'];
                } else {
                    $newCode = $this->_sqk($decCode);
                }

                break;

            case IvaoTransponderService::TYPE_NATO :

                $key = array_search($oldCode, $transponderRanges[$type]);
                if ($key != count($transponderRanges[$type])-1) {
                    $newCode = $transponderRanges[$type][$key+1];
                } else {
                    $newCode = $transponderRanges[$type][0];
                }

                break;

            default:
                break;
        }

        return $newCode;
    }

    private function _dec($code)
    {
        return base_convert($code, 8, 10);
    }

    private function _sqk($code)
    {
        return str_pad(base_convert($code, 10, 8), 4, '0', STR_PAD_LEFT);
    }
}