<?php

class KmsCi_Bootstrap {

    /**
     * @return KmsCi_CliRunnerAbstract
     */
    public static function getRunner()
    {
        if (getenv('KMSCI_RUNNER_PATH')) {
            $configPath = getenv('KMSCI_RUNNER_PATH');
            $configManager = new KmsCi_Config_Manager($configPath);
            $config = $configManager->getConfig();
            require_once($config['CliRunnerFile']);
            $className = $config['CliRunnerClass'];
            $args = getenv('KMSCI_RUNNER_ARGS');
            $args = empty($args) ? array() : json_decode($args, true);
            return new $className($config, $args, $configPath);
        } else {
            return false;
        }
    }

    /**
     * @param $runner KmsCi_CliRunnerAbstract
     * @return KmsCi_Runner_IntegrationTest_Base
     */
    public static function getIntegration($runner)
    {
        if (getenv('KMSCI_INTEGRATION_ID')) {
            $classname = KmsCi_Runner_IntegrationTests::getIntegrationClassById(getenv('KMSCI_INTEGRATION_ID'), $runner);
            $integration = new $classname($runner, getenv('KMSCI_INTEGRATION_ID'));
            return $integration;
        } else {
            return false;
        }
    }

}
