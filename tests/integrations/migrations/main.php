<?php

require_once(__DIR__.'/KmigHelper.php');

class IntegrationTests_migrations extends KmsCi_Runner_IntegrationTest_Base {

    public $kmigHelperClassName = 'IntegrationTests_migrations_KmigHelper';

    protected function _postRun()
    {
        $this->_runner->getUtilHelper()->softUnlink($this->getIntegrationPath().'/tmpdata');
        return parent::_postRun();
    }

    public function getIntegrationPath()
    {
        return __DIR__;
    }

    public function setup()
    {
        if (parent::setup()) {
            if (!$this->_runner->isArg('migrations-self-test')) {
                // delete the migrations cache file - so it will start everything from scratch each time
                $this->_runner->getUtilHelper()->softUnlink($this->getIntegrationPath().'/.kmig.phpmig.data');
            }
            return KmsCi_Kmig_IntegrationHelper::getInstance($this)->setup();
        } else {
            return false;
        }
    }

    public function testDummy()
    {
        $kmigHelper = KmsCi_Kmig_IntegrationHelper::getInstance($this);
        $mEntry = $kmigHelper->getMigrator()->entry->get('test123');
        $kEntry = $kmigHelper->getClient()->baseEntry->get($mEntry->id);
        if ($mEntry->name == 'test123' && $kEntry->name == 'test123') {
            echo "OK\n";
            return true;
        } else {
            echo "FAILED\n";
            return false;
        }
    }

    public function testData()
    {
        if (file_exists($this->getIntegrationPath().'/tmpdata')) {
            echo "OK\n";
            return true;
        } else {
            echo "FAILED\n";
            return false;
        }
    }

    public function testSelf()
    {
        if ($this->_runner->isArg('migrations-self-test')) {
            echo "SKIPPED\n";
            return true;
        } else {
            $util = $this->_runner->getUtilHelper();
            $util->softUnlink($this->getIntegrationPath().'/tmpdata');
            $kmsci = __DIR__.'/../../../bin/kmsci';
            $kmig = $kmsci.' --migrations-self-test --kmig migrations ';
            $ok = false;
            if ($util->exec($kmig.'--kmig-migrate')) {
                $data = json_decode(file_get_contents($this->getIntegrationPath().'/tmpdata'), true);
                $ok = ($data['initial'] && $data['runner']);
                if (!$ok) {
                    echo implode("\n", $util->getExecOutput());
                } else {
                    $ok = false;
                    if ($util->exec($kmig.'--kmig-status')) {
                        $output = $util->getExecOutput();
                        $kmigdata = file_get_contents($this->getIntegrationPath().'/.kmig.phpmig.data');
                        $kmigdata = json_decode($kmigdata, true);
                        $ok = (
                            in_array("serviceUrl: {$kmigdata['serviceUrl']}", $output)
                            && in_array("partnerId: {$kmigdata['partnerId']}", $output)
                            && in_array("secret: {$kmigdata['secret']}", $output)
                            && in_array("adminSecret: {$kmigdata['adminSecret']}", $output)
                        );
                    }
                }
            }
            echo ($ok?'OK':'FAILED')."\n";
            return $ok;
        }
    }

}
