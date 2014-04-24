<?php

spl_autoload_register(function($className){
    $parts = explode('_', $className);
    if ($parts[0] == 'KmsCi') {
        require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $parts).'.php');
    }
});

$configManager = new KmsCi_Config_Manager(getcwd());
$config = $configManager->getConfig();

function errorhelp($msg)
{
    echo "\n*** ERROR **\n\n{$msg}\n\nPlease see the usage guide at https://github.com/kaltura/kms-ci-framework/blob/master/README.md\n\n";
    exit(1);
}

if (!array_key_exists('CliRunnerFile', $config)) {
    errorhelp('CliRunnerFile configuration key does not exist');
} elseif (!file_exists($config['CliRunnerFile'])) {
    errorhelp('The file defined in CliRunnerFile does not exist ("'.$config['CliRunnerFile'].'")');
} elseif (!array_key_exists('CliRunnerClass', $config)) {
    errorhelp('CliRunnerClass configuration key does not exist');
} else {
    require($config['CliRunnerFile']);
    $className = $config['CliRunnerClass'];
    if (!class_exists($className)) {
        errorhelp('The class defined in CliRunnerClass does not exist ("'.$className.'")');
    } else {
        $runner = new $className($config, $argv);
        $runner->run();
    }
}
