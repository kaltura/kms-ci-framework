<?php

/*
 * This integration test - checks what happens when 2 migration integrations are running one after the other
 * there were some bugs with this
 */
class IntegrationTests_migrations2 extends KmsCi_Runner_IntegrationTest_Base {

    public function setup()
    {
        $this->_runner->getUtilHelper()->softUnlink($this->getIntegrationPath().'/.kmig.phpmig.data');
        if (parent::setup() && KmsCi_Kmig_IntegrationHelper::getInstance($this)->setup()) {
            return true;
        } else {
            return false;
        }
    }

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function testDummy()
    {
        echo "OK\n";
        return true;
    }

}
