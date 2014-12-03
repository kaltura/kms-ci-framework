<?php


class KmsCi_Runner_Command_Gui extends KmsCi_Runner_CommandBase {

    public function validateArgs()
    {
        return $this->_runner->isArg('gui');
    }

    public function run()
    {
        if ($this->_runner->isArg('gui')) {
            $this->_runner->log('Running the GUI server');
            $server = new StupidHttp_WebServer(null, 8066);
            $server->on('OPTIONS', '/')->call(function($c) {
                $data = $c->getRequest()->getFormData();
                var_dump($data);die;
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
            'gui-interface' => array('Interactive Gui Interface',
                'gui' =>
                    "  --gui                        run the interactive gui interface",
            ),
        );
    }

}
