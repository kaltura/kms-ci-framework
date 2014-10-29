<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */


class KmsCi_Kmig_IntegrationHelper extends KmsCi_Runner_IntegrationTest_Helper_Base {

    /** @var  \Kmig\Container */
    protected $_container = null;

    /** @var  KmsCi_Environment_PhpmigHelper_Phpmig */
    protected $_phpmig = null;

    protected static $_instances = array();

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
            'adminSecret' => 'KALTURA_ADMIN_SECRET',
            'partnerEmail' => 'KALTURA_PARTNER_EMAIL',
            'partnerPassword' => 'KALTURA_PARTNER_PASSWORD',
        );
        foreach ($keys as $configKey => $envParamKey) {
            // try first from specific integration configuration, then from default
            $v = $this->_runner->getConfig('kmig.'.$integId.'.'.$configKey, $this->_runner->getConfig('kmig.'.$configKey, ''));
            if (!empty($v)) {
                $envParams[$envParamKey] = $v;
            }
        }
        $envParams['KMSCI_RUNNER_PATH'] = $this->_runner->getConfigPath();
        $envParams['KMSCI_RUNNER_ARGS'] = json_encode($this->_runner->getUnparsedArgs());
        $envParams['KMSCI_INTEGRATION_ID'] = $this->_integration->getIntegrationId();
        return $envParams;
    }

    protected function _extraStatusDetails()
    {
        $kmigdata = file_get_contents($this->getKmigPhpmigDataFileName());
        $kmigdata = json_decode($kmigdata, true);
        echo implode("\n", array(
            'serviceUrl: '.$kmigdata['serviceUrl'],
            'partnerId: '.$kmigdata['partnerId'],
            'secret: '.$kmigdata['secret'],
            'adminSecret: '.$kmigdata['adminSecret'],
            'partnerEmail: '.$kmigdata['partnerEmail'],
            'partnerPassword: '.$kmigdata['partnerPassword'],
        ))."\n";
    }

    protected function _getPhpmigFileContents()
    {
        $integId = $this->_integration->getIntegrationId();
        $phpmig = file_get_contents(__DIR__.'/phpmig.template.php');
        $phpmig = str_replace(array(
            '/**PRE_CODE**/',
            'INTEGID',
            '/**POST_CODE**/'
        ), array(
            $this->_getPhpmigFileContents_preCode(),
            $integId,
            $this->_getPhpmigFileContents_postCode(),
        ), $phpmig);
        return $phpmig;
    }

    protected function _getPhpmigFileContents_preCode()
    {
        return ''
            ."// change this to point to your vendor autoload file\n"
            ."// or do any other autoloading you require in your migrations\n"
            ."require_once(__DIR__.'/../../../vendor/autoload.php');\n"
            ."\n"
            ."// also, remember to autoload your kaltura library - you can use kmig's library if you want\n"
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
        return 'KmsCi_Kmig_Migration';
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

    protected function _setup_runningRunnerCommand()
    {
        // when running a runner command - no need to run any migrations
        return true;
    }

    protected function _setup_notRunnerCommand()
    {
        $phpmig = $this->getPhpmig();
        try {
            if ($phpmig->isAllMigrationsRan()) {
                return $this->_setup_notRunnerCommand_allMigrationsRan();
            } elseif ($phpmig->migrate()) {
                return $this->_setup_notRunnerCommand_MigrationsRanSuccessfully();
            } else {
                return $this->_setup_notRunnerCommand_MigrationsError();
            }
        } catch (Exception $e) {
            $this->_setup_notRunnerCommand_MigrationsError();
            throw $e;
        }
    }

    protected function _setup_notRunnerCommand_allMigrationsRan()
    {
        // all migrations ran - everything is up to date
        // no need to do anything
        return true;
    }

    protected function _setup_notRunnerCommand_MigrationsRanSuccessfully()
    {
        $this->_postMigrate();
        return true;
    }

    protected function _setup_notRunnerCommand_MigrationsError()
    {
        $this->_postMigrate();
        return false;
    }

    /*
     * this method will run before running migrations
     */
    protected function _preMigrate()
    {

    }

    /*
     * this method will run after running migrations
     */
    protected function _postMigrate()
    {

    }

    /*
     * this method will run after every single migration
     * you should use it to save intermediate state in case a migration fails unexpectedly
     */
    public function postEachMigration()
    {

    }

    /**
     * @return \Kmig\Migrator
     */
    public function getMigrator()
    {
        $container = $this->getContainer();
        return $container['migrator'];
    }

    /**
     * @return \Kaltura_Client_Client
     */
    public function getClient()
    {
        $container = $this->getContainer();
        return $container['client'];
    }

    public function getContainer()
    {
        if (empty($this->_container)) {
            $this->_container = $this->getPhpmig()->getContainer();
        }
        return $this->_container;
    }

    public function getPhpmig()
    {
        if (empty($this->_phpmig)) {
            /** @var KmsCi_Environment_PhpmigHelper $helper */
            $helper = $this->_runner->getEnvironment()->getHelper('phpmig');
            $this->_phpmig = $helper->getNewPhpmig($this->_getEnvParams(), $this->_integration->getIntegrationPath(), $this->_integration->getIntegrationId());
        }
        return $this->_phpmig;
    }

    /**
     * setup the migration environment for the integration
     * @return bool
     */
    public function setup()
    {
        $this->_preMigrate();
        if (isset($this->_integration->isRunningKmigRunnerCommand) && $this->_integration->isRunningKmigRunnerCommand) {
            return $this->_setup_runningRunnerCommand();
        } else {
            return $this->_setup_notRunnerCommand();
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
        if (in_array('generate', $params) && count($params) > 1) {
            $params[count($params)-1] = $params[count($params)-1].ucfirst($integration->getIntegrationId()).'Integ';
        }
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
        } elseif (in_array('status', $params)) {
            if (file_exists($this->getKmigPhpmigDataFileName())) {
                $this->_extraStatusDetails();
            }
        }
        $this->_postMigrate();
        return $ok;
    }

    public function getKmigPhpmigDataFileName()
    {
        $configPath = $this->_runner->getConfigPath();
        if ($configPath == $this->_runner->getConfig('rootPath')) {
            $tmpPath = $this->_runner->getConfig('migrationsKmigDataPath', '');
            if (empty($tmpPath)) {
                $dataFile = $this->_integration->getIntegrationFilename('.kmig.phpmig.data');
            } else {
                $dataFile = $tmpPath.'/.kmig.phpmig.data.'.$this->_integration->getIntegrationId();
            }
        } else {
            $dataFile = $configPath.'/.kmig.phpmig.data.'.$this->_integration->getIntegrationId();
        }
        return $dataFile;
    }

    public function bootstrapContainer()
    {
        $container = new \Kmig\Container(array(
            'Kmig_Migrator_ID' => 'kmsci_integration_'.$this->_integration->getIntegrationId(),
            'Kmig_Phpmig_Adapter_DataFile' => $this->getKmigPhpmigDataFileName(),
        ));

        $container['phpmig.adapter'] = function($c) {
            return new KmsCi_Kmig_Adapter($c);
        };

        $container['phpmig.migrations_path'] = $this->_integration->getIntegrationFilename('migrations');

        return $container;
    }

    public static function getInstanceByIntegrationId($integId)
    {
        $runner = KmsCi_Bootstrap::getRunner();
        $className = KmsCi_Runner_IntegrationTests::getIntegrationClassById($integId, $runner);
        $integration = new $className($runner, $integId);
        return KmsCi_Kmig_IntegrationHelper::getInstance($integration);
    }

    /**
     * @param $integration KmsCi_Runner_IntegrationTest_Base
     * @return KmsCi_Kmig_IntegrationHelper
     */
    public static function getInstance($integration)
    {
        $integId = $integration->getIntegrationId();
        if (!array_key_exists($integId, KmsCi_Kmig_IntegrationHelper::$_instances)) {
            $className = isset($integration->kmigHelperClassName) ? $integration->kmigHelperClassName : 'KmsCi_Kmig_IntegrationHelper';
            KmsCi_Kmig_IntegrationHelper::$_instances[$integId] = new $className($integration);
        }
        return KmsCi_Kmig_IntegrationHelper::$_instances[$integId];
    }

} 