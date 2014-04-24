<?php

class KmsCi_Config_Manager {

    public function __construct($path)
    {
        $this->_path = $path;
    }

    public function getConfig()
    {
        $parts = explode(DIRECTORY_SEPARATOR, $this->_path);
        $fullparts = array('/etc');
        $lastpart = '';
        $first = true;
        foreach ($parts as $part) {
            if ($first) {
                $fullparts[] = DIRECTORY_SEPARATOR;
                $first = false;
            } else {
                $fullpart = $lastpart.DIRECTORY_SEPARATOR.$part;
                $fullparts[] = $fullpart;
                $lastpart = $fullpart;
            }
        }
        $merged_configs = array();
        foreach ($fullparts as $part) {
            if (file_exists($part.DIRECTORY_SEPARATOR.'kmsci.conf.php')) {
                $config = array();
                require($part.DIRECTORY_SEPARATOR.'kmsci.conf.php');
                $merged_configs = array_merge($merged_configs, $config);
            }
        }
        foreach ($fullparts as $part) {
            if (file_exists($part.DIRECTORY_SEPARATOR.'kmsci.conf.local.php')) {
                $config = array();
                require($part.DIRECTORY_SEPARATOR.'kmsci.conf.local.php');
                $merged_configs = array_merge($merged_configs, $config);
            }
        }
        return $merged_configs;
    }

}
