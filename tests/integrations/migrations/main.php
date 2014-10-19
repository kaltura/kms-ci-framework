<?php

class IntegrationTests_migrations extends KmsCi_Runner_IntegrationTest_Base {

    /** @var  KmsCi_Kmig_IntegrationHelper */
    protected $_kmigHelper;

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function setup()
    {
        if (parent::setup()) {
            $this->_kmigHelper = new KmsCi_Kmig_IntegrationHelper($this);
            return $this->_kmigHelper->setup();
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
