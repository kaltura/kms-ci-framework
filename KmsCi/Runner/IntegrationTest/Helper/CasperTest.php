<?php

class KmsCi_Runner_IntegrationTest_Helper_CasperTest extends KmsCi_Runner_IntegrationTest_Helper_Base
{

    public function test($testName, $dumpName = '', $params = array())
    {
        if (empty($dumpName)) {
            $dumpName = $testName;
        }
        $logDumpFilename = $this->_integration->getOutputPath().'/dump/'.$dumpName.'.casper.log';
        $jsFilename = $this->_integration->getIntegrationFilename($testName.'.casper.js');
        if ($this->_runner->getEnvironment()->getHelper('casper')->test($jsFilename, $logDumpFilename, $params)) {
            return $this->_runner->log(' OK');
        } else {
            return $this->_runner->error(' FAILED');
        }
    }

}
