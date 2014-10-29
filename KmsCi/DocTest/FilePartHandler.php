<?php

class KmsCi_DocTest_FilePartHandler extends KmsCi_DocTest_PartHandlerBase {

    protected $_handlesRegex = 'file: .*';

    public function handle($lines) {
        $firstLine = array_shift($lines);
        $filename = substr($firstLine, 6);
        echo "file: {$filename}\n";
        $content = implode("\n", $lines);
        if (!file_put_contents($filename, $content)) {
            throw new Exception('failed to create file: '.$filename);
        };
    }

} 