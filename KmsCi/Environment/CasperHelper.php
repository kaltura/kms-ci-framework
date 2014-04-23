<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Environment_CasperHelper extends KmsCi_Environment_BaseHelper {

    public function get()
    {
        $toolsDir = $this->_runner->getConfig('toolsDir', '');
        return (!empty($toolsDir) ? $toolsDir.'/' : '').'casperjs';
    }

    public function test($jsFilename, $logDumpFilename, $params)
    {
        $escapedparams = '';
        foreach ($params as $key=>$value) {
            $escapedparams .= ' '.escapeshellarg('--'.$key.'='.$value);
        };
        $casper = $this->get();
        $cmd = $casper.' test --no-colors'.$escapedparams.' '.escapeshellarg($jsFilename);
        exec($cmd, $output, $returnvar);
        if ($returnvar === 0) {
            $output = implode("\n", $output);
            file_put_contents($logDumpFilename, $output);
            return true;
        } else {
            echo $cmd."\n";
            echo implode("\n", $output);
            return false;
        }
    }

}
