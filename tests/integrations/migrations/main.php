<?php

class IntegrationTests_migrations extends KmsCi_Runner_IntegrationTest_Base {

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function setup()
    {
        // only for testing - delete the .kmig.phpmig.data file to make sure it's created properly
        $this->_runner->getUtilHelper()->softUnlink($this->getIntegrationPath().'/.kmig.phpmig.data');
        // also - change the kmig identifier each time so we will create new data each time, for real integrations
        // you can just set it to null - it will be set automatically with the integration name and some prefix
        $kmigMigratorId = uniqid();
        if (parent::setup()) {
            $helper = new KmsCi_Kmig_Helper($this->_runner);
            return $helper->setupIntegration($this->_integid, $this->getIntegrationPath(), null, $kmigMigratorId);
        } else {
            return false;
        }
    }

    public function testDummy()
    {
        echo "OK\n";
        return true;
    }

}
