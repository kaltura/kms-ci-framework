<?php

class KmsCi_Runner_IntegrationTest_Helper_Base
{

    /** @var  KmsCi_Runner_IntegrationTest_Base */
    protected $_integration;

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($integration)
    {
        $this->_integration = $integration;
        $this->_runner = $integration->getRunner();
    }

}
