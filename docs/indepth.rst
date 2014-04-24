Full documentation guide
========================

This guide will detail the concepts and methodologies that Kms-ci-framework implements. It should be possible to follow this guide without using the Kms-ci-framework code itself, it can be used as a generic guide to CI processes and methodologies.

Before starting this guide you should be familiar with :doc:`Basic continuous integration concepts <noobs>`.

The sample project
------------------

During this guide we will write tests for a sample project - a personal C.V. website written in php. Kms-ci-framework is written in php so php knowledge is required to use it. However, you may still follow along this guide without using php or the Kms-ci-framework.

We will develope the project using TDD methodology - where we will write the tests first.

Setting up kms-ci-framework for our project
-------------------------------------------

If you are not going to use Kms-ci-framework you can skip to next section: `the frontpage`_.

You should install Kms-ci-framework first, see the :doc:`installation guide <install>`.

Start by creating a directory for our project::

    $ mkdir cv
    $ cd cv

Using TDD methodology we write code only to let some test pass. So, the first "test" is to run the kms-ci-framework code::

    cv$ kmsci

You will get an error along these lines: "CliRunnerFile configuration key does not exist"

Kms-ci-framework scans for configuration files in your current directory. It looks for a file called "kmsci.conf.php". Let's create that file now::

    cv$ touch kmsci.conf.php

And, using your favorite editor, input the following content::

    <?php
    $config = array(
        'CliRunnerFile' => __DIR__.'/CliRunner.php',
    );

After running "kmsci" you will get an error: "The file defined in CliRunnerFile does not exist". Let's create that file::

    cv$ touch CliRunner.php

After running "kmsci" You will get an error: "CliRunnerClass configuration key does not exist". Let's add it to kmsci.conf.php::

    'CliRunnerClass' => 'CliRunner',

After running "kmsci" You will get an error: "The class defined in CliRunnerClass does not exist". Let's define it - edit CliRunner.php::

    <?php

    class CliRunner extends KmsCi_CliRunnerAbstract {}

KmsCi_CliRunnerAbstract is the main class of Kms-ci-framework. It will be defined (along with all the kms-ci-framework classes) when you run kmsci.

Now, when you run "kmsci" you will get the default Kms-ci-framework help message.

The frontpage
-------------

The first test we will run is to see if the frontpage web page is accessible. We do this using an integration test. We will do just the most minimal test that checks that when accessing the web page we get a 200 http status. This just makes sure everything is configured properly.

Setting up kms-ci-framework to run integration tests
----------------------------------------------------

If you are not using kms-ci-framework you can skip to the next section: `Writing a screenshot test for the frontpage`_.

To run integration tests in kms-ci-framework we pass the -i (or --integrations) parameter::

    cv$ kmsci -i

You will get a warning: "WARNING: no integrationTestsPath" but the test will pass (because there aren't any tests).

Let's create our main tests directory, and inside that our integration tests directory::

    cv$ mkdir tests
    cv$ mkdir tests/integrations

Now, we need to let kms-ci-framework know where the integration tests are located, add the following to kmsci.conf.php::

    'integrationTestsPath' => __DIR__.'/tests/integrations',

Inside this integrations directory we can have several integration classes and each class can contain several tests. For now, we will just create a "main" integration::

    cv$ mkdir tests/integrations/main

Each integration directory must have a main.php file with the integration class - tests/integrations/main/main.php::

    <?php

    class IntegrationTests_main extends KmsCi_Runner_IntegrationTest_Base {

    }

Writing a screenshot test for the frontpage
-------------------------------------------

Now, let's add our test method - this method will just try to get a screenshot of the frontpage::

    public function testFrontpage()
    {
        $helper = new KmsCi_Runner_IntegrationTest_Helper_Screenshot($this);
        return $helper->get('/', 1024, 768, 'frontpage');
    }

Kms-ci-framework provides helpers for running common testing functionality. In this case we use the screenshot helper. This helper uses phantomjs to get the screenshot.

This helper will detect if there is an http or php error when accessing the page and will also store a dump of the html received, http header and a screenshot of the page.

Setting up kms-ci-framework to run the screenshot helper
--------------------------------------------------------

You can skip to the next section if you are not using kms-ci-framework: `Testing the frontpage content`_.

Now, when you run "kmsci -i" you will get an exception: 'key "outputPath" must be set in the configuration!'. The outputPath configuration is where all the output of the different tests will be stored (log file, html dumps, screenshots etc.). Let's define it in kmsci.conf.php::

    'outputPath' => __DIR__.'/.output',

And, we should also create this directory (when using source control, it should also be ignored)::

    cv$ mkdir .output

Now, when running "kmsc -i" you will get another exception: "your integration class should define the getAbsoluteUrl method". The getAbsoluteUrl method is required to run the screenshot helper. It converts a relative url to absolute url. Let's define it, in tests/integrations/main/main.php::

    public function getAbsoluteUrl($relativeUrl)
    {
        return $this->_runner->getConfig('baseUrl').$relativeUrl;
    }

In this case we get a configuration key called 'baseUrl' from the main runner. This key should be defined in the configuration but it should not be in source control as part of the project (because it might be differente on different machines). To set local configuration you can create a file kmsci.conf.local.php::

    <?php

    $config = array(
        'baseUrl' => 'http://cvproject',
    );

This configuration will be merged with the main configuration in kmsci.conf.php. Of course, you will have to setup a webserver at the relevant domain that will serve files from your cv directory.

Also, to detect errors in the html source code, the screenshot helper needs to know the full root path of your project, add the following in kmsc.conf.php::

    'rootPath' => __DIR__,

Testing the frontpage content
-----------------------------

After setting up the basic frontpage test, when we run "kmsci -i" the test might pass, so let's check if there is any relevant content in the page. tests/integrations/main/main.php::

    public function testFrontpage()
    {
        $helper = new KmsCi_Runner_IntegrationTest_Helper_Screenshot($this);
        if (!$helper->get('/', 1024, 768, 'frontpage')) {
            return false;
        } elseif (strpos($helper->getLastHtmlContent(), 'Welcome to the frontpage of the test CV project!') === false) {
            return $this->_runner->error(' FAILED - expected content was not found');
        } else {
            return true;
        }
    }

Now, we run the test again and it will fail. So, let's create the minimal code to let the test pass, create index.html::

    <html>
        <head></head>
        <body>
            <h1>Welcome to the frontpage of the test CV project!</h1>
        </body>
    </html>

Now the test will pass.

Adding some more functionality - using CasperJS
-----------------------------------------------

Let's add a select box on our frontpage which will magically change the text. This select box will have the following options:

* Summary (the default option)
* Education
* Languages

We will write the test first.

To test webpage functionality we use `CasperJS <http://casperjs.org/>`_. Let's write a test in tests/integrations/main/frontpageTest.casper.js::

    // we must pass this parameter to casper when running the test - this is the url we will access
    var url = casper.cli.get('url');

    casper.test.begin('changing selectbox on frontpage will change the text', 9, function suite(test) {
        casper.start(url).then(function(){
            // default selection is Summary
            test.assertSelectorHasText('#selboxtext', 'Summary');
            // it's important to ensure that the other section's text does not appear
            test.assertSelectorDoesntHaveText('#selboxtext', 'Education');
            test.assertSelectorDoesntHaveText('#selboxtext', 'Languages');
            casper.evaluate(function(){
                $('#selbox').val('education').change();
            });
            test.assertSelectorDoesntHaveText('#selboxtext', 'Summary');
            test.assertSelectorHasText('#selboxtext', 'Education');
            test.assertSelectorDoesntHaveText('#selboxtext', 'Languages');
            casper.evaluate(function(){
                $('#selbox').val('languages').change();
            });
            test.assertSelectorDoesntHaveText('#selboxtext', 'Summary');
            test.assertSelectorDoesntHaveText('#selboxtext', 'Education');
            test.assertSelectorHasText('#selboxtext', 'Languages');
        });
        casper.run(function(){
            test.done();
        });
    });

Now, let's run it with casperjs::

    cv$ casperjs test tests/integrations/main/frontpageTest.casper.js --url=(The full url to your local cvproject domain)

Of course, it will fail, let's implement the relevant code in index.html::

    <html>
        <head>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        </head>
        <body>
            <h1>Welcome to the frontpage of the test CV project!</h1>
            <select id='selbox'>
                <option value='summary' selected>Summary</option>
                <option value='education'>Education</option>
                <option value='languages'>Languages</option>
            </select>
            <div id='selboxtext'>
                Summary of my CV
            </div>
            <script>
                $(function(){
                    $('#selbox').on('change', function(){
                    switch ($(this).val()) {
                        case 'education':
                        $('#selboxtext').html('My Education');break;
                        case 'languages':
                        $('#selboxtext').html('My Languages');break;
                        default:
                        $('#selboxtext').html('Summary of my CV');break;
                    };
                    });
                });
            </script>
        </body>
    </html>

Now, run the test again and it will pass.

Integrating the casper test with kms-ci-framework
-------------------------------------------------

You can skip to the next section if you are not using kms-ci-framework: `Refactor - using qunit`_.

Now, let's integrate this test into our integration test. Edit tests/integrations/main/main.php::

    public function testFrontpageSelbox()
    {
        $helper = new KmsCi_Runner_IntegrationTest_Helper_CasperTest($this);
        return $helper->test('frontpageTest', 'frontpage', array('url' => $this->getAbsoluteUrl('/')));
    }

Now, run "kmsci -i" - you will get an error: "You should implement the getIntegrationPath method to return a path where extra required files exist". This is a method that returns the path where kms-ci-framework will search for the casper test. Add it to tests/integrations/main/main.php::

    public function getIntegrationPath()
    {
	return __DIR__;
    }

Now, run "kmsci -i" and all the tests should pass.

Notice that the casper test included a dump of it's output in .output/main/dump/frontpage.casper.log

Refactor - using qunit
----------------------

When using TDD methodology, when we are satisifed with the tests we should refactor the code. A possible refactoring in this case will be to put all the js code into a separate file.

We can also unit test this code. To test JS code we use `qunit <https://qunitjs.com/>`_.

Let's write the qunit test in tests/frontpageTest.html::

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>frontpage tests</title>
        <link rel="stylesheet" href="/tests/resources/qunit.css">
    </head>
    <body>
    <div id="qunit"></div>
    <div id="qunit-fixture"></div>
    <script src="/tests/resources/qunit.js"></script>
    <script src="/main.js"></script>
    <script>
        test("frontpage selbox", function() {
            ok(getSelboxtext('summary').indexOf('Summary') > -1);
            ok(getSelboxtext('education').indexOf('Education') > -1);
            ok(getSelboxtext('languages').indexOf('Languages') > -1);
        });
    </script>
    </body>
    </html>

Now, to run the tests we just need to access that url (/tests/frontpageTest.html). If your project uses .htaccess or other web server processing you might want to create another virtual host that will serve just the plain html files.

You will get some 404 errors::

    GET tests/resources/qunit.css 404 (Not Found) frontpageTest.html:6
    GET tests/resources/qunit.js 404 (Not Found) frontpageTest.html:11

the tests/resources/* files are the qunit source files. You can get them from the qunit project or copy them from the kms-ci-framework source under tests/resources/

After placing those files if you access /tests/frontpageTest.html again you will see the qunit framework html output.

Our test will fail because we haven't separated the js into an external file yet. Let's put the relevant js code in /main.js::

    function getSelboxtext(selboxval) {
        switch (selboxval) {
            case 'education':
                return 'My Education';
            case 'languages':
                return 'My Languages';
            default:
                return 'Summary of my CV';
        }
    }

Now, access /tests/frontpageTest.html and the tests will pass.

Setting up kms-ci-framework to run the qunit test
-------------------------------------------------

You can skip this if you are not using kms-ci-framework: `Making sure the entire testing suite runs correctly after refactoring frontpage js`_.

Kms-ci-framework automatically detects qunit tests. Just run "kmsci -q" - you will get an error "qunitTestsPath is not configured". So, let's add that configuration to kmsci.conf.php::

    'qunitTestsPath' => __DIR__.'/tests',

Now you will get an exception 'key "qunitWebServerBasePath" must be set in the configuration!'. Kms-ci-framework needs to know the full path from where your web server is serving the qunit files. Add it to kmsci.conf.php::

    'qunitWebServerBasePath' => __DIR__,

Now, an exception - 'key "qunitUrl" must be set in the configuration!' - this is the base url that serves the qunit tests. It can be the same as the baseUrl we configured previously in kmsci.conf.local.php but without the http, so just the domain name::

    'qunitUrl' => 'cvproject',

Now, run the qunit tests "kmsci -q" and they should pass.

Making sure the entire testing suite runs correctly after refactoring frontpage js
----------------------------------------------------------------------------------

Now, if we run our integration test it will pass but it still doesn't use the new js file we wrote.

To test it, let's change the text of a section. First, change the test tests/frontpage.html::

    ok(getSelboxtext('languages').indexOf('English, Spanish') > -1);

Now, run the qunit test - it will fail. Let's fix the code in main.js::

        case 'languages':
            return 'English, Spanish';

Ok, now, let's change the casper test::

    // default selection is Summary
    test.assertSelectorHasText('#selboxtext', 'Summary');
    // it's important to ensure that the other section's text does not appear
    test.assertSelectorDoesntHaveText('#selboxtext', 'Education');
    test.assertSelectorDoesntHaveText('#selboxtext', 'English, Spanish');
    casper.evaluate(function(){
        $('#selbox').val('education').change();
    });
    test.assertSelectorDoesntHaveText('#selboxtext', 'Summary');
    test.assertSelectorHasText('#selboxtext', 'Education');
    test.assertSelectorDoesntHaveText('#selboxtext', 'English, Spanish');
    casper.evaluate(function(){
        $('#selbox').val('languages').change();
    });
    test.assertSelectorDoesntHaveText('#selboxtext', 'Summary');
    test.assertSelectorDoesntHaveText('#selboxtext', 'Education');
    test.assertSelectorHasText('#selboxtext', 'English, Spanish');

The casper test will fail. Now, let's make change index.html to use the new main.js file::

    <html>
        <head>
            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
            <script src="/main.js"></script>
        </head>
        <body>
            <h1>Welcome to the frontpage of the test CV project!</h1>
        <select id='selbox'>
            <option value='summary' selected>Summary</option>
            <option value='education'>Education</option>
            <option value='languages'>Languages</option>
        </select>
        <div id='selboxtext'>
            Summary of my CV
        </div>
        <script>
        $(function(){
            $('#selbox').on('change', function(){
                $('#selboxtext').html(getSelboxtext($(this).val()));
            });
        });
        </script>
        </body>
    </html>

Now, if we run the complete testing suite, everything should be OK::

    cv$ kmsci -a

Adding some php code
--------------------

Let's add some php code so we can test it using `PHPUnit <http://phpunit.de/>`_.

We'll add some code that returns the days that passed since a certain date. Let's write the test in tests/DaysCounterTest.php::

    <?php

    require_once(__DIR__.'/../DaysCounter.php');

    class DaysCounterTest extends PHPUnit_Framework_TestCase {

        public function test()
        {
            $epoch = mktime(0, 0, 0, 01, 01, 2007);
            $counter = new DaysCounter($epoch);
            $this->assertEquals(round((time()-$epoch) / 86400), $counter->get());
        }

    }

Run this test::

    cv$ phpunit DaysCounterTest tests/DaysCounterTest.php

Of course, it will fail, let's write the code in /DaysCounter.php::

    <?php

    class DaysCounter
    {

        public function __construct($epoch)
        {
            $this->_epoch = $epoch;
        }

        public function get()
        {
            return round((time()-$this->_epoch) / 86400);
        }

    }

Now the test will pass.

Setting up kms-ci-framework to run the unit test
------------------------------------------------

You can skip to the next section if you are not using kms-ci-framework - `Adding the DaysCounter to the frontpage`_.

Run kmsci with -t parameter to run the unit tests::

    cv$ kmsci -t

You will get an error: "testsPath is not configured". This is the directory where kms-ci-framework will search for tests. Let's configure it in kmsci.conf.php::

    'testsPath' => __DIR__.'/tests',

You will get an error: "key "buildPath" must be set in the configuration!". This is a path that kms-ci-framework uses to store build artifacts. Let's add it to kmsci.conf.php::

    'buildPath' => __DIR__.'/.build',

That's it, now the test should work.

Adding the DaysCounter to the frontpage
---------------------------------------

Let's change index.html to index.php and add the days counter::

    Days since last job: <?php
        require_once(__DIR__.'/DaysCounter.php');
        $counter = new DaysCounter(mktime(0, 0, 0, 01, 01, 2007));
        echo $counter->get();
    ?>

Now, even without writing any more tests, our test suite will ensure that there isn't an unexpected php error in that code. And also, we can be fairly confident that this addition didn't add any regression bugs.