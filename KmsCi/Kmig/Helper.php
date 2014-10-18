<?php

class KmsCi_Kmig_Helper {

    /** @var  KmsCi_CliRunnerAbstract */
    protected $_runner;

    public function __construct($runner)
    {
        $this->_runner = $runner;
    }

    /**
     * @param $integId
     * @param $integrationPath string
     * @param null $envParams
     * @return bool
     */
    public function setupIntegration($integId, $integrationPath, $envParams = null, $kmigMigratorId = null)
    {
        if (empty($envParams)) $envParams = array();
        if (empty($kmigMigratorId)) $kmigMigratorId = 'kmsci_integration_'.$integId;
        $envParams = array_merge($envParams, $this->_getEnvParams($integId));
        /** @var KmsCi_Environment_PhpmigHelper $helper */
        $helper = $this->_runner->getEnvironment()->getHelper('phpmig');
        if (!$helper->exec($envParams, $integrationPath.'/phpmig.php', array('migrate'))) {
            return false;
        } else {
            $container = new \Kmig\Container();
            $datafilename = $integrationPath.'/.kmig.phpmig.data';
            \Kmig\Helper\Phpmig\KmigAdapter::setContainerValuesFromDataFile($container, $datafilename);
            $container['Kmig_Migrator_ID'] = $kmigMigratorId;
            return true;
        }
    }

    /**
     * @param $ret boolean
     * @return bool
     */
    public function CliRunner_validateArgs($ret)
    {
        return ($ret || $this->_runner->isArg('kmig'));
    }

    /**
     * @param $ret boolean
     * @throws Exception
     * @return bool
     */
    public function CliRunner_run($ret)
    {
        $integid = $this->_runner->getArg('kmig', '');
        if (empty($integid)) {
            $ok = true;
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
            /** @var KmsCi_Environment_PhpmigHelper $helper */
            $this->_runner->getUtilHelper()->setExecPassthru();
            $phpmigExists = file_exists($integration->getIntegrationPath().'/phpmig.php');
            if (file_exists($integration->getIntegrationPath().'/migrations')) {
                $curMigs = glob($integration->getIntegrationPath().'/migrations/*.php');
            } else {
                $curMigs = array();
            }
            $curcwd = getcwd();
            chdir($integration->getIntegrationPath());
            $helper = $this->_runner->getEnvironment()->getHelper('phpmig');
            $bootstrapfile = in_array('init', $params) ? '' : $integration->getIntegrationPath().'/phpmig.php';
            $ok = $helper->exec($this->_getPhpmigEnvParams($integid), $bootstrapfile, $params);
            chdir($curcwd);
            $this->_runner->getUtilHelper()->setExecPassthru(false);
            if (in_array('init', $params) && !$phpmigExists) {
                echo "\nmodifying generated files for kaltura-migrations\n";
                $phpmig = $this->_getPhpmigFileContents($integid);
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
        }
        return $ok ? $ret : false;
    }

    protected function _getPhpmigEnvParams($integid)
    {
        return array(
            'KALTURA_SERVICE_URL' => $this->_runner->getConfig("kmig.{$integid}.serviceUrl", $this->_runner->getConfig('kmig.serviceUrl', '')),
            'KALTURA_PARTNER_ID' => $this->_runner->getConfig("kmig.{$integid}.partnerId", $this->_runner->getConfig('kmig.partnerId', '')),
            'KALTURA_ADMIN_SECRET' => $this->_runner->getConfig("kmig.{$integid}.adminSecret", $this->_runner->getConfig('kmig.adminSecret', '')),
            'KALTURA_ADMIN_CONSOLE_USER' => $this->_runner->getConfig("kmig.{$integid}.adminConsoleUser", $this->_runner->getConfig('kmig.adminConsoleUser', '')),
            'KALTURA_ADMIN_CONSOLE_PASSWORD' => $this->_runner->getConfig("kmig.{$integid}.adminConsolePassword", $this->_runner->getConfig('kmig.adminConsolePassword', '')),
            'KALTURA_DEFAULT_SERVER_DOMAIN' => $this->_runner->getConfig("kmig.{$integid}.defaultServerDomain", $this->_runner->getConfig('kmig.defaultServerDomain', '')),
            'KALTURA_DEFAULT_PASSWORD' => $this->_runner->getConfig("kmig.{$integid}.defaultPassword", $this->_runner->getConfig('kmig.defaultPassword', '')),
        );
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

    protected function _getPhpmigFileContents($integId)
    {
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
            ."require_once(__DIR__.'/../../../../vendor/kaltura/kmig/lib/Kaltura/autoload.php');\n"
        ;
    }

    protected function _getPhpmigFileContents_postCode()
    {
        return '';
    }

    protected function _getEnvParams($integId)
    {
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

}
