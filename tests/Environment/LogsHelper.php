<?php

class KmsCiFramework_Environment_LogsHelper extends KmsCi_Environment_LogsHelper {

    public function clear()
    {
        return true;
    }

    public function copyTo($dest)
    {
        $src = $this->_runner->getConfig('buildPath').'/testproj/testUnitTestsBootstrap.log';
        return $this->_runner->getUtilHelper()->softCopy($src, $dest.'/testUnitTestsBootstrap.log');
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
