<?php

class KmsCi_Runner_IntegrationTest_Helper_Phpunit extends KmsCi_Runner_IntegrationTest_Helper_Base
{

    protected function getCmd($classname, $filename, $switches)
    {
        $phpunit = $this->_runner->getEnvironment()->getHelper('php')->getPhpUnit();
        return $phpunit.(empty($switches)?'': ' '.$switches).' '.escapeshellarg($classname).' '.escapeshellarg($filename);
    }

    public function test($filename, $classname, $switches)
    {
        $cmd = $this->getCmd($filename, $classname, $switches);
        exec($cmd, $output, $returnvar);
        if ($returnvar === 0) {
            echo " OK\n";
            return true;
        } else {
            echo " FAILED\n";
            echo $cmd."\n";
            echo implode("\n", $output);
            return false;
        }
    }

}
