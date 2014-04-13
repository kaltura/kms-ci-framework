<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

/*
 * This is the CliRunner for the kms-ci-framework tests
 * It should not be used by other projects
 */

require(__DIR__.'/Environment.php');

class KmsCiFramework_CliRunner extends KmsCi_CliRunnerAbstract {

    protected function _run()
    {
        $ret = parent::_run();
        // make sure the relevant testproj helpers ran
        if (isset($GLOBALS['RAN_testUnitTestsBootstrap']) && $GLOBALS['RAN_testUnitTestsBootstrap']) {
            $logfile = $this->getConfig('buildPath').'/output/testproj/logs/testUnitTestsBootstrap.log';
            if (!file_exists($logfile)) {
                $ret = $this->error('did not find '.$logfile);
            } else {
                $tmp = explode("\n", file_get_contents($logfile));
                $ret = ($tmp[count($tmp)-2] == 'OK') ? $ret : false;
            }
        }
        return $ret;
    }

    public function _getNewEnvironment()
    {
        return new KmsCiFramework_Environment($this);
    }

}
