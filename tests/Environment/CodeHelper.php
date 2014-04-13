<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

/*
 * This helper sets up a test project for kms-ci-framework which we then test against
 * (it does it only for testproj integration)
 */
class KmsCiFramework_Environment_CodeHelper extends KmsCi_Environment_CodeHelper {

    protected function setup()
    {
        $path = $this->_runner->getConfig('buildPath').'/testproj';
        if (file_exists($path)) {
            return $this->_runner->error('testproj path already exists');
        } elseif (!$this->_runner->getUtilHelper()->mkdir($path)) {
            return false;
        } else {
            $kmsciconf = '<?php $config = '.var_export(array(
                'CliRunnerFile' => $path.'/CliRunner.php',
                'CliRunnerClass' => 'TestProj_CliRunner',
                'buildPath' => $path.'/.build',
                'outputPath' => $path.'/.output',
                'testsPath' => $path.'/',
                'testsBootstrapFile' => $path.'/bootstrap.php',
            ), true).';';
            file_put_contents($path.'/kmsci.conf.php', $kmsciconf);
            $clirunner = <<<STRING
<?php
class TestProj_CliRunner extends KmsCi_CliRunnerAbstract {}

STRING;
            file_put_contents($path.'/CliRunner.php', $clirunner);
            return true;
        }
    }

    protected function restore()
    {
        $path = $this->_runner->getConfig('buildPath').'/testproj';
        if (file_exists($path)) {
            return $this->_runner->getUtilHelper()->rrmdir($path);
        } else {
            return true;
        }
    }

    public function invoke($evtName, $evtParams)
    {
        // this is bad practice - you should not run different things based on integration id
        // it's used here only for testing purposes
        if (!isset($evtParams[0]) || $evtParams[0] == 'testproj') {
            return parent::invoke($evtName, $evtParams);
        } else {
            return true;
        }
    }

}
