<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Runner_Tests extends KmsCi_Runner_Base {

    public function __construct($runner)
    {
        parent::__construct($runner);
        $this->_testsPath = $this->_getTestsPath();
    }

    /*** methods which are overridden by qunit test runner ***/

    protected function _getTestsPath()
    {
        $testsPath = $this->_runner->getConfig('testsPath', '');
        !empty($testsPath) || $this->_runner->log('testsPath is not configured');
        return $testsPath;
    }

    protected function _setup()
    {
        return true;
    }

    protected function _getNewTestCasesFinder($testsPath)
    {
        return new KmsCi_Runner_Tests_TestCasesFinder($this->_runner, $this->_testsPath);
    }

    protected function _getNewTestCasesRunner($all_tests, $filterString, $testsPath)
    {
        return new KmsCi_Runner_Tests_TestCases($all_tests, $filterString, $testsPath, $this->_runner);
    }

    /**********************************************************/

    public function run()
    {
        if (empty($this->_testsPath)) {
            return true;
        } else {
            if (!$this->_setup()) {
                return false;
            }
            $filterString = $this->_runner->getArg('filter', '');
            $finder = $this->_getNewTestCasesFinder($this->_testsPath);
            $all_tests = $finder->get();
            $runner = $this->_getNewTestCasesRunner($all_tests, $filterString, $this->_testsPath);
            $errorTexts = $runner->run();

            if (count($errorTexts) > 0) {
                echo '-----------------------------';
                echo "\n".implode("\n-----------------------------\n", $errorTexts)."\n\n";
                echo '-----------------------------';
                echo "\nFAILURES - see details above\n";
                return false;
            } else {
                echo "\nALL TESTS PASSED\n";
                return true;
            }
        }
    }

}
