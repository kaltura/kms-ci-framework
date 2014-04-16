<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class KmsCi_Runner_Tests_TestCasesFinder {

    /** @var  Kms_Helper_Environment */
    protected $_runner;
    protected $_testCasesRootPath;

    public function __construct($runner, $testCasesRootPath)
    {
        $this->_runner = $runner;
        $this->_testCasesRootPath = $testCasesRootPath;
    }

    /*** methods overridden by qunit tests ***/

    protected function _isTestDir($dir)
    {
        return (strpos($dir->getFilename(), 'Test.php') !== false);
    }

    protected function _getClassName($str, $relfn)
    {
        if (preg_match('/class\s(\w*Test)\s/', $str, $matches) == 1) {
            return $matches[1];
        } else {
            return false;
        }
    }

    /********************************************/

    public function get()
    {
        $ans = array();
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_testCasesRootPath));
        foreach ($iterator as $dir) {
            /** @var SplFileInfo $dir */
            if ($this->_isTestDir($dir)) {
                $fn = $dir->getPath().'/'.$dir->getFilename();
                $fn = $this->_runner->getUtilHelper()->normalizePath($fn);
                $testCasesRootPath = rtrim($this->_testCasesRootPath, '/');
                $relfn = str_replace($testCasesRootPath.'/', '', $fn);
                $str = file_get_contents($fn);
                if ($className = $this->_getClassName($str, $relfn)) {
                    $ans[$relfn] = $className;
                };
            };
        }
        return $ans;
    }

}
