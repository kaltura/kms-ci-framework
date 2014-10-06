<?php

class KmsCi_Environment_BaseHelper {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($runner)
    {
        $this->_runner = $runner;
    }

    public function log($msg)
    {
        return $this->_runner->log($msg);
    }

    public function error($msg)
    {
        return $this->_runner->error($msg);
    }

    public function invoke($evtName, $evtParams)
    {
        return true;
    }

    public function getBin($execName)
    {
        $vendorDir = $this->_runner->getConfig('vendorDir', '');
        if (!empty($vendorDir) && file_exists($vendorDir.'/bin/'.$execName)) {
            return $vendorDir.'/bin/'.$execName;
        } else {
            $toolsDir = $this->_runner->getConfig('toolsDir', '');
            if (!empty($toolsDir) && file_exists($toolsDir.'/'.$execName)) {
                return $toolsDir.'/'.$execName;
            } else {
                return $execName;
            }
        };
    }

}