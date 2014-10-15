<?php

class KmsCi_Kmig_Helper {

    /**
     * @param $runner KmsCi_CliRunnerAbstract
     * @param $integrationPath string
     * @return bool
     */
    public static function setupIntegration($runner, $integId, $integrationPath, $envParams = null)
    {
        /** @var KmsCi_Environment_PhpmigHelper $helper */
        $helper = $runner->getEnvironment()->getHelper('phpmig');

        if (!$helper->exec($envParams, $integrationPath.'/phpmig.php', array('migrate'))) {
            return false;
        } else {
            $container = new \Kmig\Container();
            $datafilename = $integrationPath.'/.kmig.phpmig.data';
            \Kmig\Helper\Phpmig\KmigAdapter::setContainerValuesFromDataFile($container, $datafilename);
            $container['Kmig_Migrator_ID'] = 'kmsci_integration_'.$integId;
            return true;
        }
    }

}
