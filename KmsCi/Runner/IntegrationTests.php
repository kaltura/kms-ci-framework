<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class KmsCi_Runner_IntegrationTests extends KmsCi_Runner_Base {

    protected $_isRemote = false;

    public function setRemote($val = true)
    {
        $this->_isRemote = $val;
    }

    public function run($isSetupIntegration = false)
    {
        $testsPath = $this->_runner->getConfig('integrationTestsPath', '');
        if (empty($testsPath)) {
            $this->_runner->log('WARNING: no integrationTestsPath');
            return true;
        } elseif ($isSetupIntegration) {
            return $this->setupIntegration($this->_runner->getArg('setup-integration'));
        } else {
            $ret = true;
            foreach (glob($this->_runner->getConfig('integrationTestsPath').'/*') as $fn) {
                if (is_dir($fn)) {
                    $tmp = explode('/', $fn);
                    $integid = $tmp[count($tmp)-1];
                    $clsname = 'IntegrationTests_'.$integid;
                    $mainfn = $fn.'/main.php';
                    require_once($mainfn);
                    /** @var KmsCi_Runner_IntegrationTest_Base $tests */
                    $tests = new $clsname($this->_runner, $integid);
                    // skip non-remote integration tests
                    if ($this->_isRemote && !$tests->isRemote()) {
                        continue;
                    }
                    // skip remote integration tests
                    if (!$this->_isRemote && $tests->isRemote()) {
                        continue;
                    }
                    $filter = $this->_runner->getArg('filter', '');
                    if (empty($filter) || preg_match($filter, $integid) === 1) {
                        echo "{$clsname}: \n";
                        if (!$tests->run()) {
                            $ret = false;
                        }
                    }
                    echo "\n\n";
                }
            }
            return $ret;
        }
    }

    public function setupIntegration($integId)
    {
        $clsname = 'IntegrationTests_'.$integId;
        $mainfn = $this->_runner->getConfig('integrationTestsPath').'/'.$integId.'/main.php';
        if (!file_exists($mainfn)) {
            echo "file not found: {$mainfn}\n";
            return false;
        } else {
            require_once($mainfn);
            /** @var KmsCi_Runner_IntegrationTest_Base $tests */
            $tests = new $clsname($this->_runner, $integId);
            if (!$tests->setup()) {
                echo "Failed to setup integration\n";
                return false;
            } else {
                $filterTests = $this->_runner->getArg('filter-tests', '');
                if (empty($filterTests)) {
                    return true;
                } else {
                    return $tests->runSetupTests($filterTests);
                }
            }
        }
    }

}
