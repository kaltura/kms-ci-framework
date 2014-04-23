<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

abstract class KmsCi_Runner_IntegrationTest_BaseRemote extends KmsCi_Runner_IntegrationTest_Base
{

    public function setup()
    {
        // remote integrations don't need to setup the environment
        if (!$this->_initDirectories()) {
            return $this->_runner->error('failed to initialize integration directories');
        } elseif (!$this->_clearDirectoriesContent()) {
            return $this->_runner->error('failed to clear integration directories content');
        } else {
            return true;
        }
    }

    protected function _postRun() {
        // remote integrations don't need postRun
        return true;
    }

    public function isRemote()
    {
        return true;
    }

}