<?php

class KmsCi_Bootstrap {

    protected static $_runner = null;

    protected static $_integrations = array();

    public static function setRunner($runner)
    {
        self::$_runner = $runner;
    }

    /**
     * @return KmsCi_CliRunnerAbstract
     */
    public static function getRunner()
    {
        if (is_null(self::$_runner)) {
            if (getenv('KMSCI_RUNNER_PATH')) {
                $configPath = getenv('KMSCI_RUNNER_PATH');
                $configManager = new KmsCi_Config_Manager($configPath);
                $config = $configManager->getConfig();
                require_once($config['CliRunnerFile']);
                $className = $config['CliRunnerClass'];
                $args = getenv('KMSCI_RUNNER_ARGS');
                $args = empty($args) ? array() : json_decode($args, true);
                self::$_runner = new $className($config, $args, $configPath);
            } else {
                self::$_runner = false;
            }
        }
        return self::$_runner;
    }

    /**
     * @param $runner KmsCi_CliRunnerAbstract
     * @return KmsCi_Runner_IntegrationTest_Base
     */
    public static function getIntegration($runner)
    {
        if ($integrationId = getenv('KMSCI_INTEGRATION_ID')) {
            if (!array_key_exists($integrationId, self::$_integrations)) {
                $classname = KmsCi_Runner_IntegrationTests::getIntegrationClassById(getenv('KMSCI_INTEGRATION_ID'), $runner);
                self::$_integrations[$integrationId] = new $classname($runner, getenv('KMSCI_INTEGRATION_ID'));
            }
            return self::$_integrations[$integrationId];
        } else {
            return false;
        }
    }

}
