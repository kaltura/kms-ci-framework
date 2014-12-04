<?php


class KmsCi_Runner_Command_AdminServer extends KmsCi_Runner_CommandBase {

    protected $_process = null;
    protected $_pipes = null;

    public function validateArgs()
    {
        return $this->_runner->isArg('admin-server');
    }

    public function run()
    {
        if ($this->_runner->isArg('admin-server')) {
            $that = $this;
            $this->_runner->log('Running the admin server');
            $server = new StupidHttp_WebServer(null, 8066);
            $server->onPattern('GET', '(.*)')->call(function(StupidHttp_HandlerContext $handlerContext) use($that) {
                $that->handleRequest($handlerContext);
            });
            $server->run();
            return true;
        } else {
            return true;
        }
    }

    public function getHelpData()
    {
        return array(
            'admin-server' => array('Administration Server',
                'admin-server' =>
                    "  --admin-server               run the administration server (on port 8066)",
            ),
        );
    }

    public function handleRequest(StupidHttp_HandlerContext $handlerContext)
    {
        $handlerContext->getResponse()->setHeader('Content-Type', 'application/json');
        $ret = array(
            'ok' => false,
            'msg' => 'unexpected error'
        );
        $q = $handlerContext->getRequest()->getQueryVariables();
        if (!array_key_exists('cmd', $q)) {
            $ret['msg'] = 'missing cmd parameter';
        } else {
            switch ($q['cmd']) {
                case 'run':
                    if (!array_key_exists('params', $q)) {
                        $ret['msg'] = 'missing params parameter';
                    } elseif (!empty($this->_process) || !empty($this->_pipes)) {
                        $ret['msg'] = 'another process is already running';
                    } else {
                        $cmd = "KMSCI_RUNNER_PATH='".$this->_runner->getConfigPath()."' ".__DIR__.'/../../../bin/kmsci '.$q['params'];
                        $this->_process = proc_open($cmd, array(
                            0 => array('pipe', 'r'),
                            1 => array('pipe', 'w'),
                            2 => array('pipe', 'w'),
                        ), $this->_pipes, $this->_runner->getConfigPath(), array(
                            'KMSCI_RUNNER_PATH' => $this->_runner->getConfigPath()
                        ));
                        if (!is_resource($this->_process)) {
                            $ret['msg'] = 'failed to start process';
                            $this->_process = null;
                            $this->_pipes = null;
                        } else {
                            // close stdin so the command start working immediately
                            fclose($this->_pipes[0]);
                            $ret['ok'] = true;
                            $ret['msg'] = 'process started successfully';
                            stream_set_blocking($this->_pipes[1], false);
                            stream_set_timeout($this->_pipes[1], 0, 1);
                        }
                    }
                    break;
                case 'run_status':
                    if (empty($this->_process) || empty($this->_pipes)) {
                        $ret['msg'] = 'a process is not running';
                    } else {
                        $ret['stdout'] = '';
                        if (!feof($this->_pipes[1])) {
                            $ret['stdout'] = fread($this->_pipes[1], 1024);
                        }
                        if (feof($this->_pipes[1])) {
                            $ret['stderr'] = stream_get_contents($this->_pipes[2]);
                            $ret['done'] = true;
                            fclose($this->_pipes[1]);
                            fclose($this->_pipes[2]);
                            $ret['returnval'] = proc_close($this->_process);
                            $ret['msg'] = 'ok, process is done';
                            $this->_process = null;
                            $this->_pipes = null;
                        } else {
                            $ret['done'] = false;
                            $ret['msg'] = 'ok, process still running';
                        }
                        $ret['ok'] = true;
                    }
                    break;
            }
        }
        echo $q['callback'].'('.json_encode($ret).')';
    }

}
