<?php

class KmsCi_Config_Manager {

    public function __construct($path)
    {
        $this->_path = $path;
    }

    public function getConfig()
    {
        $configFiles = array(
            '/etc/kmsci.conf.php',
            __DIR__.'/../../../../../kmsci.conf.php',
            $this->_path.'/kmsci.conf.php',
            $this->_path.'/kmsci.conf.local.php'
        );
        $merged_configs = array();
        foreach ($configFiles as $configFile)
        {
            if (file_exists($configFile)) {
                $config = array();
                require($configFile);
                $merged_configs = array_merge($merged_configs, $config);
            }
        }
        if (getenv('KMS_CI_CONFIG')) {
            $merged_configs = array_merge($merged_configs, json_decode(getenv('KMS_CI_CONFIG'), true));
        }
        return $merged_configs;
    }

}
