<?php

/*
 * static class helper for setting up php interpreters
 */
class KmsCi_Environment_PhpmigHelper extends KmsCi_Environment_BaseHelper {

    public function exec($envParams, $bootstrapFile, $params = null)
    {
        if (empty($params)) $params = array();
        $cmd = '';
        foreach($envParams as $k=>$v) {
            $cmd.=$k.'='.escapeshellarg($v).' ';
        }
        $cmd .= $this->_runner->getUtilHelper()->getBin('phpmig');
        if (!empty($bootstrapFile)) $cmd .= ' --bootstrap='.escapeshellarg($bootstrapFile);
        foreach ($params as $k=>$v) {
            if (is_numeric($k)) {
                $cmd .= ' '.escapeshellarg($v);
            } else {
                $cmd .= '--'.$k.'='.escapeshellarg($v);
            }
        };
        return $this->_runner->getUtilHelper()->exec($cmd);
    }

}
