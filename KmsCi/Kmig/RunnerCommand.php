<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Kmig_RunnerCommand extends KmsCi_Runner_CommandBase {

    public function validateArgs()
    {
        return true;
    }

    public function run()
    {
        $integid = $this->_runner->getArg('kmig', '');
        if (empty($integid)) {
            return true;
        } else {
            $params = array();
            foreach ($this->_runner->getArgs() as $k=>$v) {
                if (strpos($k, 'kmig-') === 0) {
                    $tmp = explode('kmig-', $k);
                    $k = $tmp[1];
                    if ($v === true) {
                        $params[] = $k;
                    } else {
                        $params[$k] = $v;
                    }
                }
            }
            $className = KmsCi_Runner_IntegrationTests::getIntegrationClassById($integid, $this->_runner);
            /** @var KmsCi_Runner_IntegrationTest_Base $integration */
            $integration = new $className($this->_runner, $integid);
            $helper = new KmsCi_Kmig_IntegrationHelper($integration);
            return $helper->runRunnerCommand($params);
        }
    }

    public function getHelpData()
    {
        return array();
    }

} 