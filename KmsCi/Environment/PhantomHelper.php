<?php

class KmsCi_Environment_PhantomHelper extends KmsCi_Environment_BaseHelper
{

    public function setup()
    {
        return true;
    }

    public function get()
    {
        $toolsDir = $this->_runner->getConfig('toolsDir', '');
        return (!empty($toolsDir) ? $toolsDir.'/' : '').'phantomjs';
    }

    public function getScreenshot($url, $width, $height, $pngfile, $htmlfile, $screenshotjs = '', $extraargs = '')
    {
        $screenshotjs = empty($screenshotjs) ? $this->_runner->getKmsCiRootPath().'/js/simple_screenshot.js' : $screenshotjs;
        if (!empty($extraargs) && is_array($extraargs)) {
            $tmp = array();
            foreach ($extraargs as $arg) {
                $tmp[] = escapeshellarg($arg);
            }
            $extraargs = ' '.implode(' ', $tmp);
        }
        $phantomjs = $this->get();
        passthru($phantomjs.' '.$screenshotjs.' '
            .escapeshellarg($url).' '.escapeshellarg($width).' '
            .escapeshellarg($height).' '.escapeshellarg($pngfile).' '
            .escapeshellarg($htmlfile).$extraargs, $returnvar
        );
        return ($returnvar === 0);
    }

}