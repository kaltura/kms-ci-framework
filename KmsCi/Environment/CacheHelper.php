<?php


abstract class KmsCi_Environment_CacheHelper extends KmsCi_Environment_BaseHelper {

    abstract public function clear();

    abstract public function clear_noApache();

    public function invoke($evtName, $evtParams)
    {
        switch ($evtName) {
            case 'IntegrationTest::setup':
            case 'CliRunner::_runRestore':
            case 'CliRunner::_runSetup':
                return $this->clear();
            default:
                return parent::invoke($evtName, $evtParams);
        }
    }

}
