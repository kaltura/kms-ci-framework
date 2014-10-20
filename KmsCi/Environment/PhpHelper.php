<?php

/*
 * static class helper for setting up php interpreters
 */
class KmsCi_Environment_PhpHelper extends KmsCi_Environment_BaseHelper {

    public function setup()
    {
        return true;
    }

    public function setupPhpUnit()
    {
        return true;
    }

    public function getPhpUnit()
    {
        return $this->getBin('phpunit');
    }

    public function getPhp()
    {
        return $this->getBin('php');
    }

    public function execPhpunit($filename, $classname, $switches, $args, $preCmd = '')
    {
        if (!is_array($args)) $args = array();
        if (!is_string($switches)) $switches = '';
        $phpunit = $this->getPhpUnit();
        $cmd = (empty($preCmd)?'':$preCmd.' ')."KMSCI_RUNNER_PATH='".$this->_runner->getConfigPath()."' ".$phpunit.(empty($switches)?'': ' '.$switches).' '.escapeshellarg($classname).' '.escapeshellarg($filename);
        if (is_array($args)) {
            foreach ($args as $k=>$v) {
                $cmd .= ' '.escapeshellarg('--param-'.$k.'='.$v);
            }
        }
        if ($this->_runner->getUtilHelper()->exec($cmd)) {
            return true;
        } else {
            echo $cmd."\n";
            echo implode("\n", $this->_runner->getUtilHelper()->getExecOutput());
            return false;
        }
    }

    public function phpunitGetParam($name)
    {
        global $argv;
        foreach ($argv as $v) {
            if (strpos($v, '--param-') === 0) {
                $v = str_replace('--param-', '', $v);
                $v = explode('=', $v);
                if ($v[0] == $name) {
                    array_shift($v);
                    return implode('=', $v);
                }
            }
        }
        return false;
    }

}
