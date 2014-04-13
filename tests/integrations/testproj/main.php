<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class IntegrationTests_testproj extends KmsCi_Runner_IntegrationTest_Base {

    public function testUnitTestsBootstrap()
    {
        chdir($this->_runner->getConfig('buildPath').'/testproj');
        $cmd = 'kmsci -t';
        exec($cmd, $output, $returnvar);
        if ($returnvar === 0 && $output[count($output)-2] == 'OK') {
            $GLOBALS['RAN_testUnitTestsBootstrap'] = true;
            echo "OK\n";
            file_put_contents($this->_runner->getConfig('buildPath').'/testproj/testUnitTestsBootstrap.log', implode("\n", $output));
            return true;
        } else {
            echo "FAILED\n";
            var_dump($returnvar);
            echo implode("\n", $output)."\n";
            return false;
        }
    }

}
