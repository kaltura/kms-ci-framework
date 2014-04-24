<?php
/*
 * This is a TestCase for the kms-ci-framework itself
 * it should not be used for other projects
 */

class KmsCiFramework_Test extends PHPUnit_Framework_TestCase {

    public function test()
    {
        $this->assertTrue(true);
    }

    public function testBootstrap()
    {
        $this->assertEquals($GLOBALS['SET_FROM_BOOTSTRAP'], 'YES!');
    }

}
