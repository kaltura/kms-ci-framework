<?php

class KmsCi_DocTest_ShellPartHandler extends KmsCi_DocTest_PartHandlerBase {

    protected $_handlesRegex = '(shell|shell-passthru|shell-stderr|shell-backtick)';

    public $output;
    public $returnvar;

    public function handle($lines) {
        $firstLine = array_shift($lines);
        $usePassthru = (strpos($firstLine, 'shell-passthru') === 0);
        $getStderr = (strpos($firstLine, 'shell-stderr') === 0);
        $useBacktick = (strpos($firstLine, 'shell-backtick') === 0);
        foreach ($lines as $line) {
            if (strpos($line, '$ ') === 0) $line = substr($line, 2);
            echo '$ '.$line."\n";
            if ($useBacktick) {
                echo shell_exec($line);
            } elseif ($usePassthru) {
                passthru($line);
            } else {
                if ($getStderr) $line .= ' 2>&1';
                exec($line, $output, $this->returnvar);
                $this->output = implode("\n", $output);
                echo $this->output."\n";
            }
        }
    }

}
