<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

/*
 * This is the CliRunner for the kms-ci-framework tests
 * It should not be used by other projects
 */

require(__DIR__.'/Environment.php');

class KmsCiFramework_CliRunner extends KmsCi_CliRunnerAbstract {

    public function _getNewEnvironment()
    {
        return new KmsCiFramework_Environment($this);
    }

}
