<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Kmig_RunnerCommand extends KmsCi_Runner_CommandBase {

    /**
     * @param $integration
     * @return KmsCi_Kmig_IntegrationHelper
     */
    protected function _getIntegrationHelper($integration)
    {
        return KmsCi_Kmig_IntegrationHelper::getInstance($integration);
    }

    public function validateArgs()
    {
        return ($this->_runner->getArg('kmig', '') != '');
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
            $helper = $this->_getIntegrationHelper($integration);
            return $helper->runRunnerCommand($params);
        }
    }

    public function getHelpData()
    {
        return array(
            'kaltura-migrations' => array('Kaltura Migrations',
                'kmig' =>
                    "  --kmig INTEGID               run the phpmig command in the context of the given integration id\n"
                   ."                               prefix parameters to php mig with --kmig e.g.:\n"
                   ."                               kmsci --kmig INTEGID --kmig-init"
                )
        );
    }

} 