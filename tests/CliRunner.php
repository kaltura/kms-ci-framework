<?php
/*
 * This is the CliRunner for the kms-ci-framework tests
 * It should not be used by other projects
 */

require(__DIR__.'/Environment.php');

class KmsCiFramework_CliRunner extends KmsCi_CliRunnerAbstract {

    /** @var \KmsCi_Kmig_Helper  */
    protected $_kmigHelper;

    protected function _validateArgs()
    {
        return $this->_kmigHelper->CliRunner_validateArgs(parent::_validateArgs());
    }

    protected function _run()
    {
        $ret = $this->_kmigHelper->CliRunner_run(parent::_run());
        // make sure the relevant testproj helpers ran
        if (isset($GLOBALS['RAN_test']) && $GLOBALS['RAN_test']) {
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

    protected function _getHelpData()
    {
        return $this->_kmigHelper->CliRunner_getHelpData(parent::_getHelpData());
    }

    public function __construct($config, $args)
    {
        parent::__construct($config, $args);
        $this->_kmigHelper = new KmsCi_Kmig_Helper($this);
    }

    public function _getNewEnvironment()
    {
        return new KmsCiFramework_Environment($this);
    }

}
