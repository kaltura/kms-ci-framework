<?php

abstract class KmsCi_Runner_IntegrationTest_Base {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    protected $_integid;

    protected $_isSetupTests = false;

    public function __construct($runner, $integid)
    {
        $this->_runner = $runner;
        $this->_integid = $integid;
    }

    public function getOutputPath()
    {
        return $this->_runner->getConfig('outputPath').'/'.$this->_integid;
    }

    public function getAbsoluteUrl($relurl)
    {
        // should be implemented in extending classes, if needed
        return $relurl;
    }

    public function getRunner()
    {
        return $this->_runner;
    }

    public function getIntegrationPath()
    {
        // usually you will just do "return __DIR__;" here
        throw new Exception('You should implement the getIntegrationPath method to return a path where extra required files exist');
    }

    public function getIntegrationFilename($filename)
    {
        return $this->getIntegrationPath().'/'.$filename;
    }

    protected function _copyLogs()
    {
        /** @var KmsCi_Environment_LogsHelper $logsHelper */
        $logsHelper = $this->_runner->getEnvironment()->getHelper('logs');
        if (!is_null($logsHelper)) {
            $destPath = $this->getOutputPath().'/logs';
            echo "Copying logs to {$destPath}\n";
            return $logsHelper->copyTo($destPath);
        } else {
            return true;
        }
    }

    protected function _initDirectories()
    {
        return (
            $this->_runner->getUtilHelper()->softMkdir($this->getOutputPath().'/screenshots')
            && $this->_runner->getUtilHelper()->softMkdir($this->getOutputPath().'/dump')
            && $this->_runner->getUtilHelper()->softMkdir($this->getOutputPath().'/logs')
            && $this->_runner->getUtilHelper()->softMkdir($this->getOutputPath().'/tmp')
        );
    }

    protected function _clearDirectoriesContent()
    {
        foreach (glob($this->getOutputPath().'/screenshots/*') as $fn) {
            if (!unlink($fn)) {
                return false;
            }
        }
        foreach (glob($this->getOutputPath().'/dump/*') as $fn) {
            if (!unlink($fn)) {
                return false;
            }
        }
        foreach (glob($this->getOutputPath().'/logs/*') as $fn) {
            if (!unlink($fn)) {
                return false;
            }
        }
        foreach (glob($this->getOutputPath().'/tmp/*') as $fn) {
            if (!unlink($fn)) {
                return false;
            }
        }
        return true;
    }

    protected function _postRun() {
        return (
            $this->_runner->getEnvironment()->invoke('IntegrationTest::_postRun', array($this->_integid, $this))
            && $this->_copyLogs()
        );
    }

    protected function _runTests($filter)
    {
        $ret = true;
        // run the tests!
        foreach (get_class_methods($this) as $methodname) {
            if (strpos($methodname, 'test') === 0) {
                if (empty($filter) || preg_match($filter, $methodname) === 1) {
                    echo $methodname.": ";
                    if (!$this->$methodname()) {
                        $ret = false;
                    }
                } else {
                    //echo $methodname.": skipped\n";
                }
            }
        }
        return $ret;
    }

    public function isRemote()
    {
        return false;
    }

    public function run()
    {
        if (!$this->setup()) {
            return false;
        } else {
            $ret = $this->_runTests($this->_runner->getArg('filter-tests', ''));
            $ret = $this->_postRun() ? $ret : false;
            return $ret;
        }
    }

    public function runSetupTests($testsFilter)
    {
        $this->_isSetupTests = true;
        return $this->_runTests($testsFilter);
    }

    public function setup()
    {
        $ret = $this->_runner->getEnvironment()->invoke('IntegrationTest::setup', array($this->_integid, $this));
        if (!$ret) {
            return $this->_runner->error('failed to do initial setting up of environment for integration');
        } elseif (!$this->_initDirectories()) {
            return $this->_runner->error('failed to initialize integration directories');
        } elseif (!$this->_clearDirectoriesContent()) {
            return $this->_runner->error('failed to clear integration directories content');
        } else {
            return true;
        }
    }

}

