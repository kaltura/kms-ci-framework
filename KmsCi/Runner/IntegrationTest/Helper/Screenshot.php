<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

class KmsCi_Runner_IntegrationTest_Helper_Screenshot extends KmsCi_Runner_IntegrationTest_Helper_Base
{

    protected function _isHtmlError($str)
    {
        return (
            stripos($str, $this->_runner->getConfig('rootPath')) !== false
            && stripos($str, 'on line') !== false
        );
    }

    protected function _phantomGetScreenshot($url, $width, $height, $pngfile, $htmlfile)
    {
        return $this->_runner->getEnvironment()->getHelper('phantom')->getScreenshot($url, $width, $height, $pngfile, $htmlfile);
    }

    public function get($relurl, $width, $height, $basefilename)
    {
        if (!parse_url($relurl, PHP_URL_HOST)) {
            // it's a relative url
            $url = $this->_integration->getAbsoluteUrl($relurl);
        } else {
            $url = $relurl;
        }
        $pngfile = $this->_integration->getOutputPath().'/screenshots/'.$basefilename.'.png';
        $htmlfile = $this->_integration->getOutputPath().'/dump/'.$basefilename.'.html';
        if (!$this->_phantomGetScreenshot($url, $width, $height, $pngfile, $htmlfile)) {
            return $this->_runner->error(' FAILED!');
        } elseif ($this->_isHtmlError(file_get_contents($htmlfile))) {
            return $this->_runner->error(' FAILED - detected error in html dump');
        } else {
            return $this->_runner->log(' OK');
        }
    }

}
