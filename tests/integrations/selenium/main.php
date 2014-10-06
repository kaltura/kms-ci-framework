<?php

class IntegrationTests_selenium extends KmsCi_Runner_IntegrationTest_Base {

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function test()
    {
        $helper = new KmsCi_Runner_IntegrationTest_Helper_Phpunit($this);
        return $helper->testBrowsers($this->getIntegrationFilename('TestMe.php'), 'KmsCiFramework_TestMe');
    }

}
