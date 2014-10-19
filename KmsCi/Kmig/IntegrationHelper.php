<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Kmig_IntegrationHelper extends KmsCi_Runner_IntegrationTest_Helper_Base {

    /** @var  \Kmig\Container */
    protected $_container;

    protected function _getEnvParams()
    {
        $integId = $this->_integration->getIntegrationId();
        $envParams = array();
        $keys = array(
            'serviceUrl' => 'KALTURA_SERVICE_URL',
            'adminConsoleUser' => 'KALTURA_ADMIN_CONSOLE_USER',
            'adminConsolePassword' => 'KALTURA_ADMIN_CONSOLE_PASSWORD',
            'defaultServerDomain' => 'KALTURA_DEFAULT_SERVER_DOMAIN',
            'defaultPassword' => 'KALTURA_DEFAULT_PASSWORD',
            'partnerId' => 'KALTURA_PARTNER_ID',
            'adminSecret' => 'KALTURA_ADMIN_SECRET'
        );
        foreach ($keys as $configKey => $envParamKey) {
            // try first from specific integration configuration, then from default
            $v = $this->_runner->getConfig('kmig.'.$integId.'.'.$configKey, $this->_runner->getConfig('kmig.'.$configKey, ''));
            if (!empty($v)) {
                $envParams[$envParamKey] = $v;
            }
        }
        return $envParams;
    }

    protected function _getPhpmigFileContents()
    {
        $integId = $this->_integration->getIntegrationId();
        $phpmig = file_get_contents(__DIR__.'/phpmig.template.php');
        $phpmig = str_replace(array(
            '/**PRE_CODE**/',
            '\Kmig\Container',
            'kmsci_integration_INTEGID',
            '\Kmig\Helper\Phpmig\KmigAdapter',
            '/**POST_CODE**/'
        ), array(
            $this->_getPhpmigFileContents_preCode(),
            $this->_getPhpmigFileContents_kmigContainer(),
            'kmsci_integration_'.$integId,
            $this->_getPhpmigFileContents_kmigAdapter(),
            $this->_getPhpmigFileContents_postCode(),
        ), $phpmig);
        return $phpmig;
    }

    protected function _getPhpmigFileContents_kmigContainer()
    {
        return '\Kmig\Container';
    }

    protected function _getPhpmigFileContents_kmigAdapter()
    {
        return '\Kmig\Helper\Phpmig\KmigAdapter';
    }

    protected function _getPhpmigFileContents_preCode()
    {
        return ''
        ."// change this path to your kaltura lib or to point to your relative vendor dir\n"
        ."require_once(__DIR__.'/../../../vendor/kaltura/kmig/lib/Kaltura/autoload.php');\n"
            ;
    }

    protected function _getPhpmigFileContents_postCode()
    {
        return '';
    }

    protected function _getMigrationFileContents($migrationName)
    {
        $str = file_get_contents(__DIR__.'/migration.template.php');
        $str = str_replace(array(
            'MigrationName',
            '\Kmig\Helper\Phpmig\KmigMigration',
            '/**UP_CODE**/',
            '/**DOWN_CODE**/',
            '/**CLASS_CODE**/',
            '/**TOP_CODE**/',
        ), array(
            $migrationName,
            $this->_getMigrationFileContents_KmigMigration(),
            $this->_getMigrationFileContents_upCode(),
            $this->_getMigrationFileContents_downCode(),
            $this->_getMigrationFileContents_classCode(),
            $this->_getMigrationFileContents_topCode(),
        ), $str);
        return $str;
    }

    protected function _getMigrationFileContents_KmigMigration()
    {
        return '\Kmig\Helper\Phpmig\KmigMigration';
    }

    protected function _getMigrationFileContents_upCode()
    {
        return '';
    }

    protected function _getMigrationFileContents_downCode()
    {
        return '$this->_autoMigrateDown();';
    }

    protected function _getMigrationFileContents_classCode()
    {
        return '';
    }

    protected function _getMigrationFileContents_topCode()
    {
        return '';
    }

    /**
     * @return \Kmig\Migrator
     */
    public function getMigrator()
    {
        return $this->_container['migrator'];
    }

    /**
     * @return \Kaltura_Client_Client
     */
    public function getClient()
    {
        return $this->_container['client'];
    }

    /**
     * @param $integId
     * @param $integrationPath string
     * @param null $envParams
     * @return bool
     */
    public function setup()
    {
        if ($this->_integration->isRunningKmigRunnerCommand) {
            return true;
        } else {
            /** @var KmsCi_Environment_PhpmigHelper $helper */
            $helper = $this->_runner->getEnvironment()->getHelper('phpmig');
            if (!$helper->exec($this->_getEnvParams(), $this->_integration->getIntegrationPath().'/phpmig.php', array('migrate'))) {
                return false;
            } else {
                $container = array();
                require_once($this->_integration->getIntegrationPath().'/phpmig.php');
                $this->_container = $container;
                $datafilename = $this->_integration->getIntegrationPath().'/.kmig.phpmig.data';
                \Kmig\Helper\Phpmig\KmigAdapter::setContainerValuesFromDataFile($this->_container, $datafilename);
                return true;
            }
        }
    }

    public function runRunnerCommand($params)
    {
        $integration = $this->_integration;
        $runner = $integration->getRunner();
        $integration->isRunningKmigRunnerCommand = true;
        $integration->setup();
        $runner->getUtilHelper()->setExecPassthru();
        $phpmigExists = file_exists($integration->getIntegrationPath().'/phpmig.php');
        if (file_exists($integration->getIntegrationPath().'/migrations')) {
            $curMigs = glob($integration->getIntegrationPath().'/migrations/*.php');
        } else {
            $curMigs = array();
        }
        $curcwd = getcwd();
        chdir($integration->getIntegrationPath());
        /** @var KmsCi_Environment_PhpmigHelper $helper */
        $helper = $runner->getEnvironment()->getHelper('phpmig');
        $bootstrapfile = in_array('init', $params) ? '' : $integration->getIntegrationPath().'/phpmig.php';
        $ok = $helper->exec($this->_getEnvParams(), $bootstrapfile, $params);
        chdir($curcwd);
        $runner->getUtilHelper()->setExecPassthru(false);
        if (in_array('init', $params) && !$phpmigExists) {
            echo "\nmodifying generated files for kaltura-migrations\n";
            $phpmig = $this->_getPhpmigFileContents();
            file_put_contents($integration->getIntegrationPath().'/phpmig.php', $phpmig);
        } elseif (in_array('generate', $params)) {
            $migrationName = '';
            foreach ($params as $k=>$v) {
                if (is_numeric($k) && $v != 'generate') {
                    $migrationName = $v;
                    break;
                }
            }
            if (empty($migrationName)) {
                throw new Exception('could not find migration name');
            }
            $newMigs = glob($integration->getIntegrationPath().'/migrations/*.php');
            $newMigs = array_diff($newMigs, $curMigs);
            foreach ($newMigs as $filename) {
                echo "\nmodifying {$filename} for compatibility with kaltura-migrations\n";
                file_put_contents($filename, $this->_getMigrationFileContents($migrationName));
            }
        }
        return $ok;
    }

} 