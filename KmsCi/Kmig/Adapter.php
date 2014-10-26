<?php


class KmsCi_Kmig_Adapter extends \Kmig\Helper\Phpmig\KmigAdapter {

    protected function write($versions)
    {
        parent::write($versions);
        $integration = KmsCi_Bootstrap::getIntegration(KmsCi_Bootstrap::getRunner());
        $helper = KmsCi_Kmig_IntegrationHelper::getInstance($integration);
        $helper->postEachMigration();
    }

} 