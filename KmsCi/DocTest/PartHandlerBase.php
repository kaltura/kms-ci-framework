<?php

class KmsCi_DocTest_PartHandlerBase {

    protected $_handlesRegex='';

    public function handles($lines) {
        $firstLine = array_shift($lines);
        $re = '/^'.$this->_handlesRegex.'$/';
        return (preg_match($re, $firstLine) === 1);
    }

    public function handle($lines) {

    }

    public function exec($lines, $onlyForce = false)
    {
        if (!empty($lines)) {
            echo '<'.'?php'."\n";
            $lines = implode("\n", $lines);
            echo $lines."\n";
            eval($lines);
            echo 'php?'.'>'."\n";
        }
    }

    public function assertContains($needle, $haystack)
    {
        if (strpos($haystack, $needle) === false) {
            throw new Exception('assertContains failed, needle = "'.$needle.'"');
        }
    }

    public function assertEquals($expected, $actual)
    {
        if ($expected !== $actual) {
            throw new Exception('assertEquals failed, actual: '.var_export($actual, true));
        }
    }

    public function assertNotEquals($expected, $actual)
    {
        if ($expected === $actual) {
            throw new Exception('assertNotEquals failed, actual: '.var_export($actual, true));
        }
    }

} 