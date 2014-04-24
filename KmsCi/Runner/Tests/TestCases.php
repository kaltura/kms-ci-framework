<?php

class KmsCi_Runner_Tests_TestCases {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($testCases, $filterString, $testsPath, $runner)
    {
        $this->_testCases = $testCases;
        $this->_filterString = $filterString;
        $this->_testsPath = $testsPath;
        $this->_runner = $runner;
    }

    /*** methods overridden by qunit test runner ***/

    protected function _getNewTestCaseRunner($filename, $classname, $testsPath)
    {
        return new KmsCi_Runner_Tests_TestCase($filename, $classname, $testsPath, $this->_runner);
    }

    /**********************************************/

    public function run()
    {
        $errorTexts = array();
        foreach ($this->_testCases as $filename => $classname) {
            if (empty($this->_filterString) || preg_match($this->_filterString, $classname) === 1) {
                $runner = $this->_getNewTestCaseRunner($filename, $classname, $this->_testsPath);
                $errorTexts = array_merge($errorTexts, $runner->run());
            }
        };
        return $errorTexts;
    }

}
