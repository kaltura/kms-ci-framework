<?php

class IntegrationTests_migrations extends KmsCi_Runner_IntegrationTest_Base {

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function setup()
    {
        if (parent::setup()) {
            $helper = new KmsCi_Kmig_Helper($this->_runner);
            return $helper->setupIntegration($this->_integid, $this->getIntegrationPath());
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
