<?php

class KmsCi_Environment_BaseHelper {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($runner)
    {
        $this->_runner = $runner;
    }

    public function log($msg)
    {
        return $this->_runner->log($msg);
    }

    public function error($msg)
    {
        return $this->_runner->error($msg);
    }

    public function invoke($evtName, $evtParams)
    {
        return true;
    }

}