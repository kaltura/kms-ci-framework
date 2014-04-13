<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCiFramework_Environment_CacheHelper extends KmsCi_Environment_CacheHelper {

    public function clear()
    {
        return true;
    }

    public function clear_noApache()
    {
        return true;
    }

    public function invoke($evtName, $evtParams)
    {
        // this is bad practice - you should not run different things based on integration id
        // it's used here only for testing purposes
        if (!isset($evtParams[0]) || $evtParams[0] == 'testproj') {
            return parent::invoke($evtName, $evtParams);
        } else {
            return true;
        }
    }

}
