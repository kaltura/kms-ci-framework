<?php

abstract class KmsCi_Environment_CodeHelper extends KmsCi_Environment_BaseHelper {

    abstract protected function setup();

    abstract protected function restore();

    public function invoke($evtName, $evtParams)
    {
        switch ($evtName) {
            case 'IntegrationTest::setup':
            case 'CliRunner::_runSetup':
                return $this->setup();
            case 'CliRunner::_runRestore':
                return $this->restore();
            default:
                return parent::invoke($evtName, $evtParams);
        }
    }

}
