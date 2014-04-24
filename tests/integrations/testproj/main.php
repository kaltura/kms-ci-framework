<?php

class IntegrationTests_testproj extends KmsCi_Runner_IntegrationTest_Base {

    protected function _execKmsci($params)
    {
        if (!chdir($this->_runner->getConfig('buildPath').'/testproj')) {
            echo "FAILED\n";
            return false;
        } else {
            $cmd = 'kmsci -t';
            exec($cmd, $output, $returnvar);
            if ($returnvar === 0) {
                return $output;
            } else {
                echo "FAILED\n";
                var_dump($returnvar);
                echo implode("\n", $output)."\n";
                return false;
            }
        };
    }

    protected function _kmsciTest($params)
    {
        if ($this->_execKmsci($params) === false) {
            return false;
        } else {
            return true;
        }
    }

    public function test()
    {
        $out = $this->_execKmsci('-t');
        if ($out === false) {
            return false;
        } else {
            $GLOBALS['RAN_test'] = true;
            echo "OK\n";
            file_put_contents($this->_runner->getConfig('buildPath').'/testproj/testUnitTestsBootstrap.log', implode("\n", $out));
            return true;
        }
    }

    public function testUnitTestsBootstrap()
    {
        $path = $this->_runner->getConfig('buildPath').'/testproj';
        $test = <<<STRING
<?php
class Test extends PHPUnit_Framework_TestCase {
    public function test() { \$this->assertEquals(\$GLOBALS['SET_FROM_BOOTSTRAP'], 'YES!'); }
}
STRING;
        file_put_contents($path.'/Test.php', $test);
        $bootstrap = <<<STRING
<?php \$GLOBALS['SET_FROM_BOOTSTRAP'] = 'YES!';
STRING;
        file_put_contents($path.'/bootstrap.php', $bootstrap);
        if ($this->_isSetupTests) {
            return true;
        } else {
            return $this->_kmsciTest('-t');
        }
    }

}
