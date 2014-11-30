<?php

class KmsCi_Runner_IntegrationTest_Helper_Phpunit extends KmsCi_Runner_IntegrationTest_Helper_Base
{

    public function test($filename, $classname, $switches, $args = null)
    {
        $preCmd = "KMSCI_INTEGRATION_ID='".$this->_integration->getIntegrationId()."'";
        if ($this->_runner->getEnvironment()->getHelper('php')->execPhpunit($filename, $classname, $switches, $args, $preCmd)) {
            echo " OK\n";
            return true;
        } else {
            echo " FAILED\n";
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
        if (is_array($browsers)) {
            $seleniumHost = $this->_runner->getConfig('seleniumHost', '');
            if (!empty($seleniumHost)) {
                foreach ($browsers as $browser) {
                    $browser['baseHost'] = $seleniumHost;
                }
            }
        }
        $args = array_merge($args, array('browsers'=>$browsers));
        $browsers = json_decode($browsers);
        if (empty($browsers)) {
            echo "no browsers defined for testing\n";
            echo "SKIPPED\n";
            return true;
        } else {
            return $this->test($filename, $classname, $switches, $args);
        }
    }

    public static function getTestBrowsers()
    {
        $browsers = KmsCi_PHPUnit_Helper::getParam('browsers');
        if (empty($browsers)) $browsers = json_encode(array());
        return json_decode($browsers, true);
    }

}
