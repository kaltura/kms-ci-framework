<?php

abstract class KmsCi_Environment_ConfigHelper extends KmsCi_Environment_BaseHelper {

    abstract public function remove();

    abstract public function restore();

    public function invoke($evtName, $evtParams)
    {
        switch ($evtName) {
            case 'IntegrationTest::setup':
            case 'IntegrationTest::_postRun':
            case 'CliRunner::_runSetup':
                return $this->remove();
            case 'CliRunner::_runRestore':
                return $this->restore();
            default:
                return parent::invoke($evtName, $evtParams);
        }
    }

}
