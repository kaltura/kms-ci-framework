<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

abstract class KmsCi_Environment_CodeHelper extends KmsCi_Environment_BaseHelper {

    abstract protected function setup();

    abstract protected function restore();

    public function invoke($evtName, $evtParams)
    {
        switch ($evtName) {
            case 'IntegrationTest::setup':
                return $this->setup();
            case 'CliRunner::_runRestore':
                return $this->restore();
            default:
                return parent::invoke($evtName, $evtParams);
        }
    }

}
