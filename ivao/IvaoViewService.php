<?php

class IvaoViewService
{
    protected static $_instance;

    private function __construct()
    {}
    /**
     * @return IvaoViewService
     */
    static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    function render($flights, $fields)
    {
        echo "<table border=\"1\">\n";
        echo "<tr>\n";

        foreach ($fields as $field) {
            $cell = $field['title'];
            $class = isset($field['style']) ? " class=\"{$field['style']}\"" : '';
            preg_match_all("/{([a-zA-Z]+)}/", $field['title'], $functions);
            foreach ($functions[1] as $fun) {
                if (method_exists($this, $fun)) {
                    $cell = preg_replace("/{" . $fun . "}/", call_user_method($fun, $this), $cell);
                }
            }
            echo "<th{$class}>{$cell}</th>\n";
        }

        echo "</tr>\n";

        foreach ($flights as $flight) {

            echo "<tr class=\"{$this->getRowStyle($flight)}\">\n";
            foreach ($fields as $field) {

                $cell = $field['pattern'];
                preg_match_all("/%([a-zA-Z]+)%/", $field['pattern'], $values);
                preg_match_all("/{([a-zA-Z]+)}/", $field['pattern'], $functions);

                foreach ($values[1] as $val) {
                    if (isset($flight->{$val})) {
                        $cell = preg_replace("/%" . $val . "%/", $flight->{$val}, $cell);
                    }
                }

                foreach ($functions[1] as $fun) {
                    if (method_exists($this, $fun)) {
                        $cell = preg_replace("/{" . $fun . "}/", call_user_method($fun, $this), $cell);
                    }
                }

                $class = isset($field['style']) ? " class=\"{$field['style']}\"" : '';
                echo "<td{$class}>{$cell}</td>";

            }
            echo "</tr>\n";
        }

        echo "</table>";
    }

    function getRowStyle($flight)
    {
        if ($flight->transponder == $flight->assignedTransponder){
            return 'transponder-ok';
        }

        return '';
    }

    function now()
    {
        return date("H:i", mktime(date('H') + $GLOBALS['UtcTimeVar']));
    }
}