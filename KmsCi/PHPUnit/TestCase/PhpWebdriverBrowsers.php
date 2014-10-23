<?php

class KmsCi_PHPUnit_TestCase_PhpWebdriverBrowsers extends PHPUnit_Framework_TestCase {

    public static $browsers = array(
        array(
            // display name of the browser (appended to test name)
            'name' => 'firefox',
            // name of method from DesiredCapabilities class e.g. DesiredCapabilities::firefox()
            // if ommitted - will try to use 'name' property
            'browser' => 'firefox',
            // selenium remote host name
            // if ommitted - uses default values:
            // for chrome = http://localhost:9515
            // for firefox = http://localhost:4444/wd/hub
            'host' => 'http://localhost:4444/wd/hub',
            // timeout
            'timeout' => 30000,
            // sauce details - only if exists, will use sauce
            'sauce' => 'ACCESS_KEY:USERNAME'
        ),
    );

    public $browser;

    /** @var  RemoteWebDriver */
    public $driver;

    public function setUp()
    {
        $this->driver = self::getDriver($this->browser);
    }

    public function tearDown()
    {
        if ($this->driver) $this->driver->quit();
    }

    public function getCurrentBrowserName()
    {
        return self::getBrowserTestName($this->browser);
    }

    public static function getBrowserTestName($browser)
    {
        if (array_key_exists('name', $browser)) {
            return $browser['name'];
        } elseif (array_key_exists('browser', $browser)) {
            return $browser['browser'];
        } else {
            return 'firefox';
        }
    }

    public static function getDriver($browser)
    {
        if (array_key_exists('browser', $browser)) {
            $browserName = $browser['browser'];
        } elseif (array_key_exists('name', $browser)) {
            $browserName = $browser['name'];
        } else {
            $browserName = 'firefox';
        }
        /** @var DesiredCapabilities $capabilities */
        $capabilities = call_user_func(array('DesiredCapabilities', $browserName));
        if (array_key_exists('version', $browser)) {
            $capabilities->setVersion($browser['version']);
        }
        if (array_key_exists('platform', $browser)) {
            $capabilities->setPlatform($browser['platform']);
        }
        if (array_key_exists('sauce', $browser)) {
            $host = 'http://'.$browser['sauce'].'@ondemand.saucelabs.com:80/wd/hub';
            if (getenv('TRAVIS_JOB_NUMBER')) {
                // silly hack to set the tunnel id for travis
                // TODO: make this more generic, not dependant on travis
                $capabilities->setCapability('tunnel-identifier', getenv('TRAVIS_JOB_NUMBER'));
            }
        } elseif (array_key_exists('host', $browser)) {
            $host = $browser['host'];
        } elseif ($browserName == 'chrome') {
            $host = 'http://localhost:9515';
        } else {
            $host = 'http://localhost:4444/wd/hub';
        }
        $timeout = array_key_exists('timeout', $browser) ? $browser['timeout'] : 30000;
        $driver = RemoteWebDriver::create($host, $capabilities, $timeout);
        if (array_key_exists('sauce', $browser)) {
            echo "\nhttps://saucelabs.com/tests/".$driver->getSessionID()."\n";
        }
        return $driver;
    }

    public static function suite($className)
    {
        /** @var KmsCi_PHPUnit_TestCase_PhpWebdriverBrowsers $className */
        $suite = new PHPUnit_Framework_TestSuite();
        foreach ($className::$browsers as $browser) {
            $bsuite = new PHPUnit_Framework_TestSuite();
            $class = new ReflectionClass($className);
            $bsuite->setName($class->getName().'_'.$className::getBrowserTestName($browser));
            foreach ($class->getMethods() as $method) {
                if (strpos($method->getDeclaringClass()->getName(), 'PHPUnit_') !== 0) {
                    if ($method->isPublic() && $bsuite->isTestMethod($method)) {
                        $name = $method->getName();
                        $test = new $className($name);
                        $test->browser = $browser;
                        $bsuite->addTest($test);
                    }
                }
            }
            $suite->addTest($bsuite);
        };
        return $suite;
    }

}