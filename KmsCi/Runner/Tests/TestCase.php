<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class KmsCi_Runner_Tests_TestCase {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    protected $_tmpXmlPath;

    public function __construct($filename, $classname, $testsPath, $runner)
    {
        $this->_classname = $classname;
        $this->_testsPath = $testsPath;
        $this->_runner = $runner;
        $this->_filename = $testsPath.'/'.$filename;
        $this->_init();
        $this->_cleanup();
    }

    /*** methods which are overridden in qunit tests ***/

    protected function _init()
    {
        $this->_tmpXmlPath = $this->_runner->getConfig('buildPath').'/tmp.xml';
    }

    protected function _getCmd()
    {
        $bootstrapfile = $this->_runner->getConfig('testsBootstrapFile', '');
        $bootstrapfileparam = empty($bootstrapfile) ? '' : ' --bootstrap '.escapeshellarg($bootstrapfile);
        $phpunitcmd = $this->_runner->getEnvironment()->getHelper('php')->getPhpUnit();
        $cmd = $phpunitcmd.' --log-junit '.escapeshellarg($this->_tmpXmlPath).$bootstrapfileparam.' '.escapeshellarg($this->_classname).' '.escapeshellarg($this->_filename).' 2>&1';
        return $cmd;
    }

    protected function _cleanup()
    {
        if (file_exists($this->_tmpXmlPath)) {
            unlink($this->_tmpXmlPath);
        }
    }

    protected function _verifySuccessfulRun($cmd, $out, $returnvar)
    {
        if (!file_exists($this->_tmpXmlPath)) {
            return array(false, 1, $cmd, $out);
        } else {
            $xmlstr = file_get_contents($this->_tmpXmlPath);
            return array(true, $returnvar, $cmd, $xmlstr);
        }
    }

    protected function _parseResponseTestCase($testsuite, $testcase)
    {
        $errorTexts = array();
        foreach ($testcase->failure as $testcasefailure) {
            // $testcasefailure['type']
            // (string)$testcasefailure
            $errorTexts[] = ""
                ."Failure in {$testsuite['name']} ({$testcase['file']}:{$testcase['line']})\n"
                .(string)$testcasefailure
            ;
        }
        foreach ($testcase->error as $testcaseerror) {
            // $testcaseerror['(type)']
            // (string)$testcaseerror
            $errorTexts[] = ""
                ."Error in {$testsuite['name']} ({$testcase['file']}:{$testcase['line']})\n"
                .(string)$testcaseerror
            ;
        }
        return $errorTexts;
    }

    protected function _parseResponseTestSuite($testsuite)
    {
        $errorTexts = array();
        foreach ($testsuite->testcase as $testcase) {
            // $testcase['(name|class|file|line|assertions|time)']
            $caseErrorTexts = $this->_parseResponseTestCase($testsuite, $testcase);
            if (count($caseErrorTexts) > 0) {
                echo 'E';
                $errorTexts = array_merge($errorTexts, $caseErrorTexts);
            } else {
                echo '.';
            }
        }
        return $errorTexts;
    }

    protected function _parseResponse($xmlstr)
    {
        $errorTexts = array();
        $xml = new SimpleXMLElement($xmlstr);
        $isFirst = true;
        foreach ($xml as $testsuite) {
            // $testsuite['(name|file|tests|assertions|failures|errors|time)']
            echo ($isFirst ? '' : ' | ').$testsuite['name'].': ';
            $errorTexts = array_merge($errorTexts, $this->_parseResponseTestSuite($testsuite));
            $isFirst = false;
            echo "\n";
        }
        return $errorTexts;
    }

    protected function _setup()
    {
        is_null($this->_runner->getEnvironment()->getHelper('cache'))
            || $this->_runner->getEnvironment()->getHelper('cache')->clear_noApache();
        is_null($this->_runner->getEnvironment()->getHelper('config'))
            || $this->_runner->getEnvironment()->getHelper('config')->remove();
    }

    /******************************/

    protected function _exec()
    {
        $cmd = $this->_getCmd();
        $out = '';
        exec($cmd, $out, $returnvar);
        if ($returnvar !== 0 && $returnvar !== 1 && $returnvar !== 2) {
            return array(false, $returnvar, $cmd, $out);
        } else {
            return $this->_verifySuccessfulRun($cmd, $out, $returnvar);
        }
    }

    public function run()
    {
        $this->_setup();
        list($isOk, $returnvar, $cmd, $out) = $this->_exec();
        if ($isOk) {
            $errorTexts = $this->_parseResponse($out);
        } else {
            echo $this->_classname.": E\n";
            $errorTexts = array(""
            ."exit with return value {$returnvar} in file {$this->_filename} class {$this->_classname}\n"
            .$cmd."\n"
            ."output:\n"
            .implode("\n", $out)
            );
        }
        $this->_cleanup();
        return $errorTexts;
    }

}