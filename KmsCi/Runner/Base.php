<?php

abstract class KmsCi_Runner_Base {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($runner)
    {
        $this->_runner = $runner;
    }

    abstract public function run($params = array());

}
