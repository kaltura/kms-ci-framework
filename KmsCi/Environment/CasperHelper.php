<?php


class KmsCi_Environment_CasperHelper extends KmsCi_Environment_BaseHelper {

    public function get()
    {
        return $this->getBin('casperjs');
    }

    public function test($jsFilename, $logDumpFilename, $params)
    {
        $cmd = $this->get();
        $args = array(
            '--verbose', '--log-level=debug', '--no-colors',
            '--kmscienv='.json_encode($this->_runner->exportEnvironment()),
            $jsFilename
        );
        foreach ($params as $key=>$value) {
            $args[] = '--'.$key.'='.$value;
        };
        if (PhpCrossplatform\PHPCP::exec($cmd, array('args' => $args), $res)) {
            $output = implode("\n", $res->output);
            if (!empty($logDumpFilename)) {
                file_put_contents($logDumpFilename, $output);
            } else {
                // TODO: this should be done only if debug
                //echo $output;
            }
            return true;
        } else {
            echo $cmd."\n";
            echo implode("\n", $res->output);
            return false;
        }
    }

}
