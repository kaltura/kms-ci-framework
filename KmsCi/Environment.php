<?php

class KmsCi_Environment {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    /** @var KmsCi_Environment_BaseHelper[] */
    protected $_helpers = array();

    protected $_registeredEventsCallbacks = array();

    public function __construct($runner)
    {
        $this->_runner = $runner;
    }

    protected function _initializeHelper($name)
    {
        switch ($name) {
            case 'php':
                return new KmsCi_Environment_PhpHelper($this->_runner);
            case 'phantom':
                return new KmsCi_Environment_PhantomHelper($this->_runner);
            case 'util':
                return new KmsCi_Environment_UtilHelper($this->_runner);
            case 'casper':
                return new KmsCi_Environment_CasperHelper($this->_runner);
            case 'phpmig':
                return new KmsCi_Environment_PhpmigHelper($this->_runner);
            default:
                return null;
        }
    }

    /**
     * This method allows extending classes to modify the order of helper invocations for a specific event
     * @param $evtName
     * @param $evtParams
     * @return array of helper names
     */
    protected function _getHelperNames($evtName, $evtParams)
    {
        return array('code', 'config', 'logs', 'cache');
    }

    public function getHelper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $this->_helpers[$name] = $this->_initializeHelper($name);
        }
        return $this->_helpers[$name];
    }

    public function error($str)
    {
        $this->log($str);
        return false;
    }

    public function log($str)
    {
        // hide sensitive values
        $hideKeys = array('password', 'secret', 'login');
        $hideKeys = array_merge($hideKeys, $this->_runner->getConfig('hideConfigKeys', array()));
        foreach ($hideKeys as $k) {
            foreach ($this->_runner->getConfig() as $ck => $cv) {
                if (strpos(strtolower($ck), strtolower($k)) !== false) {
                    if (!empty($cv)) {
                        $str = str_replace($cv, '*****', $str);
                    }
                }
            }
        }
        echo $str."\n";
        return true;
    }

    public function on($evtName, $callback)
    {
        if (!array_key_exists($evtName, $this->_registeredEventsCallbacks)) {
            $this->_registeredEventsCallbacks[$evtName] = array();
        }
        $this->_registeredEventsCallbacks[$evtName][] = $callback;
    }

    public function invoke($evtName, $evtParams = array(), $breakOnError = true)
    {
        $stop = false;
        $ret = true;
        foreach ($this->_getHelperNames($evtName, $evtParams) as $helperName) {
            $helper = $this->getHelper($helperName);
            if (!is_null($helper)) {
                if (!$helper->invoke($evtName, $evtParams)) {
                    $this->error('failed invoke of "'.$evtName.'", with params: '.print_r($evtParams, true));
                    $ret = false;
                    if ($breakOnError) {
                        $stop = true;
                        break;
                    }
                }
            }
        }
        if (!$stop && array_key_exists($evtName, $this->_registeredEventsCallbacks)) {
            foreach ($this->_registeredEventsCallbacks[$evtName] as $callback) {
                if (!call_user_func_array($callback, $evtParams)) {
                    $this->error('failed invoke callback of "'.$evtName.'"');
                    $ret = false;
                    if ($breakOnError) break;
                }
            }
        }
        return $ret;
    }

}
