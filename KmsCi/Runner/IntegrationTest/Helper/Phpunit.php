<?php

class KmsCi_Runner_IntegrationTest_Helper_Phpunit extends KmsCi_Runner_IntegrationTest_Helper_Base
{

    protected function getCmd($classname, $filename, $switches, $args = null)
    {
        if (!is_array($args)) $args = array();
        if (!is_string($switches)) $switches = '';
        $phpunit = $this->_runner->getEnvironment()->getHelper('php')->getPhpUnit();
        $cmd = $phpunit.(empty($switches)?'': ' '.$switches).' '.escapeshellarg($classname).' '.escapeshellarg($filename);
        if (is_array($args)) {
            foreach ($args as $k=>$v) {
                $cmd .= ' '.escapeshellarg('--param-'.$k.'='.$v);
            }
        }
        return $cmd;
    }

    public function test($filename, $classname, $switches, $args = null)
    {
        if (!is_array($args)) $args = array();
        if (!is_string($switches)) $switches = '';
        $cmd = $this->getCmd($filename, $classname, $switches, $args);
        if ($this->_runner->getUtilHelper()->exec($cmd)) {
            echo " OK\n";
            return true;
        } else {
            echo " FAILED\n";
            echo $cmd."\n";
            echo implode("\n", $this->_runner->getUtilHelper()->getExecOutput());
            return false;
        }
    }

    public function testBrowsers($filename, $classname, $switches = null, $args = null)
    {
        if (!is_array($args)) $args = array();
        if (!is_string($switches)) $switches = '';
        $browsers = $this->_runner->getArg('browsers', '');
        if (empty($browsers)) {
            $sauce = $this->_runner->getConfig('sauceLabsLogin', '');
            if (empty($sauce)) {
                // just use local firefox
                // (you will need to run the selenium java server)
                // TODO: allow to modify this
                $browsers = array(array('name' => 'firefox',),);
            } else {
                // define a default set of browsers to test on sauce
                // TODO: allow to modify this
                $browsers = array(
                    array('name' => 'internetExplorer','sauce' => $sauce,'version' => '8', 'platform' => 'Windows XP',),
                    array('name' => 'internetExplorer','sauce' => $sauce,'version' => '9', 'platform' => 'Windows 7',),
                    array('name' => 'internetExplorer','sauce' => $sauce,'version' => '10', 'platform' => 'Windows 8',),
                    array('name' => 'firefox','sauce' => $sauce,'version' => 'beta', 'platform' => 'Windows 8',),
                    array('name' => 'chrome','sauce' => $sauce,'version' => 'beta', 'platform' => 'Windows 8',),
                );
            }
            $browsers = json_encode($browsers);
        }
        $args = array_merge($args, array('browsers'=>$browsers));
        return $this->test($filename, $classname, $switches, $args);
    }

    public static function getTestBrowsers()
    {
        $browsers = KmsCi_PHPUnit_Helper::getParam('browsers');
        if (empty($browsers)) $browsers = json_encode(array());
        return json_decode($browsers, true);
    }

}
