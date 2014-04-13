<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class KmsCi_Environment {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    /** @var KmsCi_Environment_BaseHelper[] */
    protected $_helpers = array();

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
            default:
                return null;
        }
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
        echo $str."\n";
        return true;
    }

}