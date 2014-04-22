<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

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
