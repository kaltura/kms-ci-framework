<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


abstract class KmsCi_Environment_CacheHelper extends KmsCi_Environment_BaseHelper {

    abstract public function clear();

    abstract public function clear_noApache();

}