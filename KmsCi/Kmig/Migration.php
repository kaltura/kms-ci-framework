<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Kmig_Migration extends \Kmig\Helper\Phpmig\KmigMigration {

    /**
     * @return KmsCi_CliRunnerAbstract
     */
    protected function _runner()
    {
        return $this->_container['kmsci.runner'];
    }

} 