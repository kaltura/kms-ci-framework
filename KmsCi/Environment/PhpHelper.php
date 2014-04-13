<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

/*
 * static class helper for setting up php interpreters
 */
class KmsCi_Environment_PhpHelper extends KmsCi_Environment_BaseHelper {

    public function setup()
    {
        return true;
    }

    public function setupPhpUnit()
    {
        return true;
    }

    public function getPhpUnit()
    {
        $toolsDir = $this->_runner->getConfig('toolsDir', '');
        return (!empty($toolsDir) ? $toolsDir.'/' : '').'phpunit';
    }

    public function getPhp()
    {
        $toolsDir = $this->_runner->getConfig('toolsDir', '');
        return (!empty($toolsDir) ? $toolsDir.'/' : '').'php';
    }

}
