<?php


class KmsCi_Environment_CasperHelper extends KmsCi_Environment_BaseHelper {

    public function get()
    {
        return $this->getBin('casperjs');
    }

    public function test($jsFilename, $logDumpFilename, $params)
    {
        $params['kmscienv'] = json_encode($this->_runner->exportEnvironment());
        $escapedparams = '';
        foreach ($params as $key=>$value) {
            $escapedparams .= ' '.escapeshellarg('--'.$key.'='.$value);
        };
        $casper = $this->get();
        $cmd = $casper.' test --no-colors'.$escapedparams.' '.escapeshellarg($jsFilename);
        exec($cmd, $output, $returnvar);
        if ($returnvar === 0) {
            $output = implode("\n", $output);
            if (!empty($logDumpFilename)) {
                file_put_contents($logDumpFilename, $output);
            } else {
                echo $output;
            }
            return true;
        } else {
            echo $cmd."\n";
            echo implode("\n", $output);
            return false;
        }
    }

}
