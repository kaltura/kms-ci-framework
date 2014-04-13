<?php

/*
 * This is the configuration for the kms-ci-framework tests
 * you can use this as reference for your project's configuration
 */

$config = array(
    /*
     * Required configurations for all projects
     */
    // File containing the main CliRunner for your project
    'CliRunnerFile' => __DIR__.'/tests/CliRunner.php',
    // Class name of the main CliRunner for your project
    'CliRunnerClass' => 'KmsCiFramework_CliRunner',
    // Build artifacts will be saved here
    'buildPath' => __DIR__.'/.build',
    // Output artifacts will be saved here
    'outputPath' => __DIR__.'/.build/output',

    /*
     * PHPUNIT tests
     */
    // under this path the tests runner will look for tests
    'testsPath' => __DIR__.'/tests',
    // (optional) a bootstrap file
    'testsBootstrapFile' => __DIR__.'/tests/bootstrap.php',

    /*
     * qunit tests
     */
    // path to look for qunit tests in
    'qunitTestsPath' => __DIR__.'/tests',
    // base path that the web servers serves files under
    'qunitWebServerBasePath' => __DIR__,

    /*
     * Integration tests
     */
    // path to look for integration test directories in
    'integrationTestsPath' => __DIR__.'/tests/integrations',
);
