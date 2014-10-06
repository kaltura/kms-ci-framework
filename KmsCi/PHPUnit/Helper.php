<?php

class KmsCi_PHPUnit_Helper {

    public static function getParam($name, $default = '')
    {
        global $argv;
        foreach ($argv as $v) {
            if (strpos($v, '--param-') === 0) {
                $v = str_replace('--param-', '', $v);
                $v = explode('=', $v);
                if ($v[0] == $name) {
                    array_shift($v);
                    return implode('=', $v);
                }
            }
        }
        return false;
    }

}