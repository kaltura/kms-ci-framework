<?php

class KmsCi_Runner_IntegrationTest_Helper_CasperTest extends KmsCi_Runner_IntegrationTest_Helper_Base
{

    public function test($testName, $dumpName = '', $params = array())
    {
        $params['kmsciint'] = json_encode($this->_integration->exportEnvironment());
        if (empty($dumpName)) {
            $dumpName = $testName;
        }
        $logDumpFilename = $this->_integration->getOutputPath().'/dump/'.$dumpName.'.casper.log';
        $jsFilename = $this->_integration->getIntegrationFilename($testName.'.casper.js');
        /** @var KmsCi_Environment_CasperHelper $helper */
        $helper = $this->_runner->getEnvironment()->getHelper('casper');
        if ($helper->test($jsFilename, $logDumpFilename, $params)) {
            return $this->_runner->log(' OK');
        } else {
            return $this->_runner->error(' FAILED');
        }
    }

}
