<?php
/*
 * All Code Confidential and Proprietary, Copyright ©2011 Kaltura, Inc.
 * To learn more: http://corp.kaltura.com/Products/Video-Applications/Kaltura-Mediaspace-Video-Portal
 */

require_once(__DIR__.'/Environment.php');

abstract class KmsCi_CliRunnerAbstract {

    protected $_config = array();
    protected $_args = array();

    /** @var  KmsCi_Environment */
    protected $_environment;

    public function __construct($config, $args)
    {
        $this->_config = $config;
        $this->_args = $this->_parseArgs($args);
        $this->_environment = $this->_getNewEnvironment();
    }

    /**
     * You can override this function to change these definitions
     * It's encouraged to leave them so that all runners will have common options
     * @return array of short keys to long keys
     */
    protected function _getShortKeysToLongKeys()
    {
        return array(
            'a' => 'all',
            't' => 'tests',
            'i' => 'integrations',
            's' => 'setup',
            'r' => 'restore',
            'c' => 'clear',
            'f' => 'filter',
            'm' => 'remote',
            'b' => 'build',
            'q' => 'qunit',
        );
    }

    /**
     * Help data is an array in the following format:
     * array(
     *     'section_id' => array('section title',
     *         'long_option_key' => 'long option help text'
     *     )
     * )
     * You can change it in extending classes but it's better not to so all kms ci classes will work the same
     * @return array of help data
     */
    protected function _getHelpData()
    {
        return array(
            'misc' => array('Miscellaneous Options',
                'all' =>
                    "  -a, --all                    run all the integration and unit-tests",
                'build' =>
                    "  -b, --build                  prepare the environment for ant build",
                'clear' =>
                    "  -c, --clear                  clear the environment",
                'restore' =>
                    "  -r, --restore                restore the environment (after setup or setup-integration)",
            ),
            'test-filtering' => array('Test filtering',
                'filter' =>
                    "  -fREGEX, --filter REGEX      regular expression filter of tests\n"
                   ."                               (unit tests on class name, integrations on integration id)",
                'filter-tests' =>
                    "  --filter-tests REGEX         more fine-grained filtering of individual test methods"
            ),
            'remote-integrations' => array('Running remote integration tests',
                'remote-integrations' =>
                    "  -m, --remote-integrations    run only the remote integration tests"
            ),
            'unit-tests' => array('Running unit tests',
                'tests' =>
                    "  -t, --tests                  run only the unit tests",
                'qunit' =>
                    "  -q, --qunit                  run the qunit js unit tests",
                'setup' =>
                    "  -s, --setup                  setup the environment for debugging unit tests (use -r to restore afterwards)"
            ),
            'integrations' => array('Running integration tests',
                'integrations' =>
                    "  -i, --integrations           run only the integration tests",
                'setup-integration' =>
                    "  --setup-integration INTEGID  setup environment for a specific integration (use -r to restore afterwards)"
            )
        );
    }

    protected function _parseArgs($args)
    {
        $longKeys = array();
        $shortKeys = array();
        $lastLongKey = '';
        foreach ($args as $arg) {
            if (strpos($arg, '--') === 0) {
                if (!empty($lastLongKey)) $longKeys[$lastLongKey] = true;
                $lastLongKey = substr($arg, 2);
            } elseif (strpos($arg, '-') === 0) {
                if (!empty($lastLongKey)) $longKeys[$lastLongKey] = true;
                $lastLongKey = '';
                $shortVal = substr($arg, 2);
                $shortKeys[substr($arg, 1, 1)] = ($shortVal === false) ? true : $shortVal;
            } else {
                if (!empty($lastLongKey)) $longKeys[$lastLongKey] = $arg;
                $lastLongKey = '';
            }
        }
        if (!empty($lastLongKey)) {
            $longKeys[$lastLongKey] = true;
        }
        $shortKeysToLongKeys = $this->_getShortKeysToLongKeys();
        foreach ($shortKeys as $key=>$val) {
            if (isset($shortKeysToLongKeys[$key])) {
                $longKeys[$shortKeysToLongKeys[$key]] = $val;
            } else {
                $longKeys[$key] = $val;
            }
        }
        return $longKeys;
    }

    public function isArg($key)
    {
        return (isset($this->_args[$key]) && $this->_args[$key]);
    }

    public function getArg($key, $default = '')
    {
        return isset($this->_args[$key]) ? $this->_args[$key] : $default;
    }

    public function getConfig($key = '', $default = null)
    {
        if (empty($key)) {
            return $this->_config;
        } elseif (!isset($this->_config[$key]) && is_null($default)) {
            throw new Exception('key "'.$key.'" must be set in the configuration!');
        } else {
            return isset($this->_config[$key]) ? $this->_config[$key] : $default;
        }
    }

    public function getEnvironment()
    {
        return $this->_environment;
    }

    public function error($msg)
    {
        return $this->_environment->error($msg);
    }

    public function log($msg)
    {
        return $this->_environment->log($msg);
    }

    /**
     * @return KmsCi_Environment_UtilHelper
     */
    public function getUtilHelper()
    {
        return $this->_environment->getHelper('util');
    }

    protected function _setAll()
    {
        $this->_args['tests'] = true;
        $this->_args['integrations'] = true;
        $this->_args['qunit'] = true;
    }

    protected function _validateArgs()
    {
        if ($this->isArg('all')) {
            $this->_setAll();
        }
        return (
            $this->isArg('setup') || $this->isArg('restore') || $this->isArg('clear')
            || $this->isArg('tests') || $this->isArg('integrations') || $this->isArg('remote')
            || $this->isArg('qunit') || $this->isArg('build')
            || $this->getArg('setup-integration')
        );
    }

    abstract protected function _getNewEnvironment();

    protected function _runRunner($runnerId, $title, $runnerClassName)
    {
        echo "\n{$title}\n\n";
        $runner = new $runnerClassName($this);
        $tmp = $runner->run();
        echo "\n";
        return $tmp;
    }

    protected function _runTests()
    {
        return $this->_runRunner('tests', 'Running tests', 'KmsCi_Runner_Tests');
    }

    protected function _runQunitTests()
    {
        return $this->_runRunner('qunit-tests', 'Running qunit tests', 'KmsCi_Runner_QunitTests');
    }

    protected function _runIntegrations()
    {
        return $this->_runRunner('integrations', 'Running integration tests', 'KmsCi_Runner_IntegrationTests');
    }

    protected function _runIntegrationsRemote()
    {

    }

    protected function _runSetup()
    {

    }

    protected function _setupIntegration()
    {

    }

    protected function _runRestore()
    {
        $ret = true;
        $ret = is_null($this->_environment->getHelper('code'))
            || $this->_environment->getHelper('code')->restore() ? $ret : $this->error('Failed to restore code');
        $ret = is_null($this->_environment->getHelper('config'))
            || $this->_environment->getHelper('config')->restore() ? $ret : $this->error('Failed to restore config');
        $ret = is_null($this->_environment->getHelper('cache'))
            || $this->_environment->getHelper('cache')->clear() ? $ret : $this->error('Failed to clear the cache');
        return $ret;
    }

    protected function _runBuild()
    {

    }

    protected function _run()
    {
        $ret = true;
        // run the tests / integrations
        $ret = (!$this->isArg('tests') || $this->_runTests()) ? $ret : false;
        $ret = (!$this->isArg('qunit') || $this->_runQunitTests()) ? $ret : false;
        $ret = (!$this->isArg('integrations') || $this->_runIntegrations()) ? $ret : false;
        $ret = (!$this->isArg('remote') || $this->_runIntegrationsRemote()) ? $ret : false;
        // do other funcs
        if ($this->isArg('setup')) {
            echo "\nSetting up the environment\n\n";
        }
        $ret = (!$this->isArg('setup') || $this->_runSetup()) ? $ret : false;
        $ret = (!$this->getArg('setup-integration') || $this->_setupIntegration()) ? $ret : false;
        if ($this->isArg('restore')) {
            echo "\nRestoring the environment\n\n";
        }
        $ret = ($this->isArg('setup') || $this->getArg('setup-integration') || $this->_runRestore()) ? $ret : false;

        if ($this->isArg('build')) {
            echo "\nBuilding for ant\n\n";
        }
        $ret = (!$this->isArg('build') || $this->_runBuild()) ? $ret : false;
        return $ret;
    }

    public function getKmsCiRootPath()
    {
        return __DIR__.'/..';
    }

    public function help()
    {
        echo "\n".'usage: kmsci [OPTIONS]...'."\n\n";
        foreach ($this->_getHelpData() as $sectionData) {
            echo $sectionData[0]."\n\n";
            foreach ($sectionData as $optionId=>$optionHelpText) {
                if (!is_numeric($optionId)) {
                    echo $optionHelpText."\n";
                }
            }
            echo "\n";
        }
    }

    public function run()
    {
        echo "config: ";
        print_r($this->_config);
        if (!$this->_validateArgs()) {
            $this->help();
        } elseif ($this->_run()) {
            echo "\n\nOK\n\n";
            exit(0);
        } else {
            echo "\n\n*******\nFAILED! SEE ERRORS ABOVE\n*********\n";
            exit(1);
        }
    }

}
