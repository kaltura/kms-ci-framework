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

    public function execPhpunit($filename, $classname, $args = null, $env = null)
    {
        if (empty($args)) $args = array();
        if (empty($env)) $env = array();
        $cmd = $this->getPhpUnit();
        $env = array_merge(
            array('KMSCI_RUNNER_PATH' => $this->_runner->getConfigPath()),
            $env
        );
        $args = array_merge(
            $args,
            array($classname, $filename)
        );

        $phpunit = $this->getPhpUnit();
        $cmd = (empty($preCmd)?'':$preCmd.' ')."KMSCI_RUNNER_PATH='".$this->_runner->getConfigPath()."' ".$phpunit.(empty($switches)?'': ' '.$switches).' '.KmsCi_Environment_UtilHelper::escapeShellArgument($classname).' '.KmsCi_Environment_UtilHelper::escapeShellArgument($filename);
        if (is_array($args)) {
            foreach ($args as $k=>$v) {
                $cmd .= ' '.KmsCi_Environment_UtilHelper::escapeShellArgument('--param-'.$k.'='.$v);
            }
        }
        if ($this->_runner->getUtilHelper()->exec($cmd)) {
            return true;
        } else {
            $this->log($cmd);
            $this->log(implode("\n", $this->_runner->getUtilHelper()->getExecOutput()));
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
