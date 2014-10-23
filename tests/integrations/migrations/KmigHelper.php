<?php

class IntegrationTests_migrations_KmigHelper extends KmsCi_Kmig_IntegrationHelper {

    protected function _preMigrate()
    {
        $data = $this->getMigrator()->exists('kmscitestdata') ? $this->getMigrator()->get('kmscitestdata') : array('FIRST!' => true);
        file_put_contents($this->_integration->getIntegrationPath().'/tmpdata', json_encode($data));
    }

    protected function _postMigrate()
    {
        $data = json_decode(file_get_contents($this->_integration->getIntegrationPath().'/tmpdata'), true);
        $this->getMigrator()->set('kmscitestdata', $data);
    }

}