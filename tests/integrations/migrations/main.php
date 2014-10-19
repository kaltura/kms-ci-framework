<?php

class IntegrationTests_migrations extends KmsCi_Runner_IntegrationTest_Base {

    /** @var  KmsCi_Kmig_Helper */
    protected $_kmigHelper;

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
            $this->_kmigHelper = new KmsCi_Kmig_Helper($this->_runner);
            return $this->_kmigHelper->setupIntegration($this->_integid, $this->getIntegrationPath(), null, $kmigMigratorId);
        } else {
            return false;
        }
    }

    public function testDummy()
    {
        $mEntry = $this->_kmigHelper->getMigrator()->entry->get('test123');
        $kEntry = $this->_kmigHelper->getClient()->baseEntry->get($mEntry->id);
        if ($mEntry->name == 'test123' && $kEntry->name == 'test123') {
            echo 'OK';
            return true;
        } else {
            echo 'FAILED';
            return false;
        }
    }

}
