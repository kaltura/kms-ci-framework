<?php

class KmsCi_Runner_CommandBase {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($runner)
    {
        $this->_runner = $runner;
    }

    public function validateArgs()
    {
        return true;
    }

    public function run()
    {
        return true;
    }

    public function getHelpData()
    {
        return array();
    }

} 