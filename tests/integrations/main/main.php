<?php

class IntegrationTests_main extends KmsCi_Runner_IntegrationTest_Base {

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function testDummy()
    {
        echo "OK\n";
        return true;
    }

    public function testCasper()
    {
        $helper = new KmsCi_Runner_IntegrationTest_Helper_CasperTest($this);
        return $helper->test('test', 'test', array(
            'param1' => 'FOO!',
            'integId' => $this->getIntegrationId(),
            'integPath' => $this->getIntegrationPath(),
            'integOutput' => $this->getOutputPath()
        ));
    }

    public function testPhpunit()
    {
        $helper = new KmsCi_Runner_IntegrationTest_Helper_Phpunit($this);
        $filename = $this->getIntegrationFilename('Test.php');
        $classname = 'Test';
        $args = array(
            'param1' => 'FOO!',
            'integId' => $this->getIntegrationId(),
            'integPath' => $this->getIntegrationPath(),
            'integOutput' => $this->getOutputPath()
        );
        return $helper->test($filename, $classname, null, $args);
    }

}
