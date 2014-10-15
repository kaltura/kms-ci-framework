<?php

class IntegrationTests_migrations extends KmsCi_Runner_IntegrationTest_Base {

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function setup()
    {
        if (parent::setup()) {
            return KmsCi_Kmig_Helper::setupIntegration($this->_runner, $this->_integid, $this->getIntegrationPath());
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
