<?php

class KmsCi_Environment_PhantomHelper extends KmsCi_Environment_BaseHelper
{

    public function setup()
    {
        return true;
    }

    public function get()
    {
        return $this->getBin('phantomjs');
    }

    public function getScreenshot($url, $width, $height, $pngfile, $htmlfile, $screenshotjs = '', $extraargs = '')
    {
        $screenshotjs = empty($screenshotjs) ? $this->_runner->getKmsCiRootPath().'/js/simple_screenshot.js' : $screenshotjs;
        if (!empty($extraargs) && is_array($extraargs)) {
            $tmp = array();
            foreach ($extraargs as $arg) {
                $tmp[] = KmsCi_Environment_UtilHelper::escapeShellArgument($arg);
            }
            $extraargs = ' '.implode(' ', $tmp);
        }
        $phantomjs = $this->get();
        $cmd = $phantomjs.' '.$screenshotjs.' '
            .KmsCi_Environment_UtilHelper::escapeShellArgument($url).' '.KmsCi_Environment_UtilHelper::escapeShellArgument($width).' '
            .KmsCi_Environment_UtilHelper::escapeShellArgument($height).' '.KmsCi_Environment_UtilHelper::escapeShellArgument($pngfile).' '
            .KmsCi_Environment_UtilHelper::escapeShellArgument($htmlfile).$extraargs;
        //var_dump($cmd);die;
        passthru($cmd, $returnvar);
        return ($returnvar === 0);
    }

    public function run($jsFilename, $extraargs = '')
    {
        $phantomjs = $this->get();
        if (!empty($extraargs) && is_array($extraargs)) {
            $tmp = array();
            foreach ($extraargs as $arg) {
                $tmp[] = KmsCi_Environment_UtilHelper::escapeShellArgument($arg);
            }
            $extraargs = ' '.implode(' ', $tmp);
        }
        exec($phantomjs.' '.KmsCi_Environment_UtilHelper::escapeShellArgument($jsFilename).$extraargs, $output, $returnvar);
        if ($returnvar === 0) {
            return true;
        } else {
            echo implode("\n", $output);
            return false;
        };
    }

}