<?php
/*
 * This is a TestCase for the kms-ci-framework itself
 * it should not be used for other projects
 */

require_once(__DIR__.'/../../../vendor/autoload.php');

class Test extends PHPUnit_Framework_TestCase {

    public function test()
    {
        $runner = KmsCi_Bootstrap::getRunner();
        /** @var KmsCi_Environment_PhpHelper $phpHelper */
        $phpHelper = $runner->getEnvironment()->getHelper('php');
        $this->assertEquals('FOO!', $phpHelper->phpunitGetParam('param1'));
        $this->assertEquals('BAR', $runner->getConfig('FOO'));
        $this->assertTrue($runner->isArg('FOO'));
        $this->assertEquals('BAR', $runner->getArg('FOO'));
        $integration = KmsCi_Bootstrap::getIntegration($runner);
        $this->assertEquals($phpHelper->phpunitGetParam('integId'), $integration->getIntegrationId());
        $this->assertEquals($phpHelper->phpunitGetParam('integPath'), $integration->getIntegrationPath());
        $this->assertEquals($phpHelper->phpunitGetParam('integOutput'), $integration->getOutputPath());
    }

}
