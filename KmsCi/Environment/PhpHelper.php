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

}
