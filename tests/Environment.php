<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

/*
 * This is the Environment for the kms-ci-framework tests
 * It should not be used by other projects
 */

require_once(__DIR__.'/Environment/CacheHelper.php');
require_once(__DIR__.'/Environment/CodeHelper.php');
require_once(__DIR__.'/Environment/ConfigHelper.php');
require_once(__DIR__.'/Environment/LogsHelper.php');

class KmsCiFramework_Environment extends KmsCi_Environment {

    protected function _initializeHelper($name)
    {
        switch ($name) {
            case 'cache':
                return new KmsCiFramework_Environment_CacheHelper($this->_runner);
            case 'code':
                return new KmsCiFramework_Environment_CodeHelper($this->_runner);
            case 'config':
                return new KmsCiFramework_Environment_ConfigHelper($this->_runner);
            case 'logs':
                return new KmsCiFramework_Environment_LogsHelper($this->_runner);
            default:
                return parent::_initializeHelper($name);
        }
    }

}
