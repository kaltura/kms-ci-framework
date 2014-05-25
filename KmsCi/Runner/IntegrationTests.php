<?php

class KmsCi_Runner_IntegrationTests extends KmsCi_Runner_Base {

    protected function _run($integid, $clsname, $isRemote = false)
    {
        $ret = true;
        /** @var KmsCi_Runner_IntegrationTest_Base $tests */
        $tests = new $clsname($this->_runner, $integid);
        // skip integrations
        if ($tests->isSkipRun()) {
            return true;
        }
        // skip non-remote integration tests
        if ($isRemote && !$tests->isRemote()) {
            return true;
        }
        // skip remote integration tests
        if (!$isRemote && $tests->isRemote()) {
            return true;
        }
        $filter = $this->_runner->getArg('filter', '');
        if (empty($filter) || preg_match($filter, $integid) === 1) {
            echo "{$clsname}: \n";
            if (!$tests->run()) {
                $ret = false;
            }
        }
        echo "\n\n";
        return $ret;
    }

    protected function _setup($integId, $clsname)
    {
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

    protected function _runAll($params)
    {
        $isRemote = (isset($params['isRemote']) && $params['isRemote']);
        $ret = true;
        foreach (glob($this->_runner->getConfig('integrationTestsPath').'/*') as $fn) {
            if (is_dir($fn)) {
                $tmp = explode('/', $fn);
                $integid = $tmp[count($tmp)-1];
                $clsname = 'IntegrationTests_'.$integid;
                $mainfn = $fn.'/main.php';
                if (file_exists($mainfn)) {
                    require_once($mainfn);
                    $ret = $this->_run($integid, $clsname, $isRemote) ? $ret : false;
                }
            }
        }
        return $ret;
    }

    public function run($params = array())
    {
        $testsPath = $this->_runner->getConfig('integrationTestsPath', '');
        if (empty($testsPath)) {
            $this->_runner->log('WARNING: no integrationTestsPath');
            return true;
        } elseif (isset($params['isSetupIntegration']) && $params['isSetupIntegration']) {
            return $this->setupIntegration($this->_runner->getArg('setup-integration'));
        } else {
            return $this->_runAll($params);
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
            return $this->_setup($integId, $clsname);
        }
    }

}
