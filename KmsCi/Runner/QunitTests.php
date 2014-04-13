<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class QunitTestCasesFinder extends KmsCi_Runner_Tests_TestCasesFinder {

    protected function _isTestDir($dir)
    {
        return (strpos($dir->getFilename(), 'Test.html') !== false);
    }

    protected function _getClassName($str, $relfn)
    {
        if (strpos($str, 'qunit') !== false) {
            return $relfn;
        } else {
            return false;
        }
    }

}

class QunitTestCases extends KmsCi_Runner_Tests_TestCases {

    protected function _getNewTestCaseRunner($filename, $classname, $testsPath)
    {
        return new QunitTestCase($filename, $classname, $testsPath, $this->_runner);
    }

}

class QunitTestCase extends KmsCi_Runner_Tests_TestCase {

    protected function _init() {}

    protected function _cleanup() {}

    protected function _setup() {}

    protected function _getCmd()
    {
        $filename = str_replace($this->_runner->getConfig('qunitWebServerBasePath'), '', $this->_filename);
        return $this->_runner->getEnvironment()->getHelper('phantom')->get().' '.$this->_runner->getKmsCiRootPath().'/js/qunit_runner.js http://'.$this->_runner->getConfig('qunitUrl').$filename;
    }

    protected function _verifySuccessfulRun($cmd, $out, $returnvar)
    {
        if ($returnvar !== 0) {
            return array(false, 1, $cmd, $out);
        } else {
            return array(true, 0, $cmd, $out);
        }
    }

    protected function _parseResponse($out)
    {
        echo $this->_classname.": .\n";
        return array();
    }

}

class KmsCi_Runner_QunitTests extends KmsCi_Runner_Tests {

    protected function _getTestsPath()
    {
        $testsPath = $this->_runner->getConfig('qunitTestsPath', '');
        !empty($testsPath) || $this->_runner->log('qunitTestsPath is not configured');
        return $testsPath;
    }

    protected function _getNewTestCasesFinder($testsPath)
    {
        return new QunitTestCasesFinder($this->_runner, $this->_testsPath);
    }

    protected function _getNewTestCasesRunner($all_tests, $filterString, $testsPath)
    {
        return new QunitTestCases($all_tests, $filterString, $testsPath, $this->_runner);
    }

}
