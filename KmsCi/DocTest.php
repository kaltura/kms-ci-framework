<?php

class KmsCi_DocTest {

    protected $_filename;

    public function __construct($filename)
    {
        $this->_filename = $filename;
        $this->_partHandlers = array('KmsCi_DocTest_ShellPartHandler', 'KmsCi_DocTest_FilePartHandler');
    }

    public function run($skipToPart = null)
    {
        $str = file_get_contents($this->_filename);
        $str = str_replace("\r\n", "\n", $str);
        $tmp = explode(' >>~ ', $str);
        array_shift($tmp);
        $i = 1;
        foreach ($tmp as $str) {
            $onlyForce = (!empty($skipToPart) && $i<$skipToPart);
            echo "\n*** docpart {$i} ***\n\n";
            $this->_runPart($str, $onlyForce);
            $i++;
        }
        echo "\nOK, all tests completed successfully\n";
        return true;
    }

    protected function _runPart($str, $onlyForce = false)
    {
        $lines = $this->_getPartLines($str);
        list ($lines, $pre, $post) = $this->_getPrePostExecs($lines, $onlyForce);
        if ($onlyForce) {
            $handler = new KmsCi_DocTest_PartHandlerBase();
            $handler->exec($pre, true);
            $handler->exec($post, true);
        } else {
            $isHandled = false;
            foreach ($this->_partHandlers as $handler) {
                if (is_string($handler)) $handler = new $handler();
                if ($handler->handles($lines)) {
                    $handler->exec($pre);
                    $handler->handle($lines);
                    $handler->exec($post);
                    $isHandled = true;
                    break;
                }
            }
            if (!$isHandled) {
                $firstLine = array_shift($lines);
                throw new Exception('part was not handled: '.$firstLine);
            }
        }
    }

    protected function _getPrePostExecs($lines, $onlyForce = false)
    {
        $newLines = array();
        $pre = array();
        $post = array();
        foreach ($lines as $line) {
            if (
                (!$onlyForce && strpos($line, '>>: ') === 0)
                || strpos($line, '>>! ') === 0
            ) {
                $line = substr($line, 4);
                if (empty($newLines)) $pre[] = $line;
                else $post[] = $line;
            } else {
                $newLines[] = $line;
            }
        }
        return array($newLines, $pre, $post);
    }

    protected function _getPartLines($str)
    {
        $lines = array();
        $tmp = explode("\n", $str);
        $lines[] = array_shift($tmp);
        preg_match('/^(\s+)/', $tmp[0], $matches);
        $tab = $matches[0];
        foreach ($tmp as $line) {
            if (strpos($line, $tab) === 0) {
                $lines[] = substr($line, strlen($tab));
            } else {
                break;
            }
        }
        return $lines;
    }

}

require_once(__DIR__.'/../vendor/autoload.php');

$docTest = new KmsCi_DocTest(__DIR__.'/../docs/indepth.rst');
$docTest->run($argv[1]);

echo "\n";