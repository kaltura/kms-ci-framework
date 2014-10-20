<?php

require_once(__DIR__.'/../../../vendor/autoload.php');

class KmsCiFramework_TestMe extends KmsCi_PHPUnit_TestCase_PhpWebdriverBrowsers {

    public function test()
    {
        $d = $this->driver;
        $d->get('http://'.KmsCi_PHPUnit_Helper::getParam('qunitUrl').'/tests/integrations/selenium/');
        $d->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName('h1')));
        $this->assertEquals('Hello World!', $d->findElement(WebDriverBy::tagName('h1'))->getText());
        $this->assertEquals('Selenium Tests!', $d->getTitle());
    }

    public function testEnvironment()
    {
        $runner = KmsCi_Bootstrap::getRunner();
        $integration = KmsCi_Bootstrap::getIntegration($runner);
        $this->assertEquals('selenium', $integration->getIntegrationId());
    }

}

KmsCiFramework_TestMe::$browsers = KmsCi_Runner_IntegrationTest_Helper_Phpunit::getTestBrowsers();
