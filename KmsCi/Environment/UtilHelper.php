<?php


class KmsCi_Environment_UtilHelper extends KmsCi_Environment_BaseHelper {

    protected $_lastExecOutput;
    protected $_lastExecReturnvar;
    protected $_execPassthru = false;


    /**
     * @param string $filename
     * @return bool if file exists - ensure to unlink, if it doesn't exist return true
     */
    public function softUnlink($filename)
    {
        if (file_exists($filename) && !unlink($filename)) {
            return $this->error('failed to unlink '.$filename);
        }
        return true;
    }

    public function softRename($old, $new)
    {
        if (file_exists($old) && !rename($old, $new)) {
            return $this->error('failed to rename '.$old.' to '.$new);
        }
        return true;
    }

    // recursively remove a directory
    public function rrmdir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                if (!$this->rrmdir($file)) {
                    return $this->error('failed to recursively remove directory '.$file);
                }
            } elseif (!$this->softUnlink($file)) {
                return false;
            }
        }
        if (!rmdir($dir)) {
            return $this->error('failed to rmdir '.$dir);
        } else {
            return true;
        }
    }

    public function softCopy($src, $dest)
    {
        if (!file_exists($src)) {
            return true;
        } else {
            return copy($src, $dest) ? true : $this->error('Failed to copy from '.$src.' to '.$dest);
        }
    }

    public function mkdir($path)
    {
        return mkdir($path) ? true : $this->error('failed to create directory '.$path);
    }

    public function softMkdir($path)
    {
        if (file_exists($path)) {
            return true;
        } else {
            return mkdir($path, 0777, true);
        }
    }

    public function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function getExecReturnvar()
    {
        return $this->_lastExecReturnvar;
    }

    public function getExecOutput()
    {
        return $this->_lastExecOutput;
    }

    public function setExecPassthru($execPassthru = true)
    {
        $this->_execPassthru = $execPassthru;
    }

    public function exec($cmd, $env = array())
    {
		$param = json_encode(array(
			'cmd' => $cmd,
			'env' => $env,
			'passthru' => $this->_execPassthru
		));
        $ncmd = $this->_runner->getEnvironment()->getHelper('php')->getPhp();
		$ncmd .= ' '.__DIR__.'/../../bin/exec.php'.' '.KmsCi_Environment_UtilHelper::escapeShellArgument($param);
		$this->_runner->verbose('>> '.$ncmd);
        $this->_lastExecOutput = array();
        $this->_lastExecReturnvar = '';
        var_dump($ncmd);
        if ($this->_execPassthru) {
            passthru($ncmd, $this->_lastExecReturnvar);
        } else {
            exec($ncmd, $this->_lastExecOutput, $this->_lastExecReturnvar);
        }
        $this->_runner->debug('== '.$this->_lastExecReturnvar);
        $this->_runner->debug(implode("\n", $this->_lastExecOutput));
        return ($this->_lastExecReturnvar === 0);
    }

    public static function escapeShellArgument($argument)
    {
        //stolen from symphony (src/Symfony/Component/Process/ProcessUtils.php)
        //Fix for PHP bug #43784 escapeshellarg removes % from given string
        //Fix for PHP bug #49446 escapeshellarg doesn't work on Windows
        //@see https://bugs.php.net/bug.php?id=43784
        //@see https://bugs.php.net/bug.php?id=49446
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            if ('' === $argument) {
                return escapeshellarg($argument);
            }
            $escapedArgument = '';
            $quote =  false;
            foreach (preg_split('/(")/i', $argument, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $part) {
                if ('"' === $part) {
                    $escapedArgument .= '\\"';
                } elseif (self::isSurroundedBy($part, '%')) {
                    // Avoid environment variable expansion
                    $escapedArgument .= '^%"'.substr($part, 1, -1).'"^%';
                } else {
                    // escape trailing backslash
                    if ('\\' === substr($part, -1)) {
                        $part .= '\\';
                    }
                    $quote = true;
                    $escapedArgument .= $part;
                }
            }
            if ($quote) {
                $escapedArgument = '"'.$escapedArgument.'"';
            }
            return $escapedArgument;
        }
        return escapeshellarg($argument);
    }
    
    private static function isSurroundedBy($arg, $char)
    {
        return 2 < strlen($arg) && $char === $arg[0] && $char === $arg[strlen($arg) - 1];
    }
    
}
