<?php
/**PRE_CODE**/

$container = new \Kmig\Container(array(
    'Kmig_Migrator_ID' => 'kmsci_integration_INTEGID',
    'Kmig_Phpmig_Adapter_DataFile' => __DIR__. DIRECTORY_SEPARATOR . '.kmig.phpmig.data',
));

$container['phpmig.adapter'] = function($c) {
    return new \Kmig\Helper\Phpmig\KmigAdapter($c);
};

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

$container['kmsci.runner'] = function($c) {
    if (getenv('KMSCI_RUNNER_PATH')) {
        $configPath = getenv('KMSCI_RUNNER_PATH');
        $configManager = new KmsCi_Config_Manager($configPath);
        $config = $configManager->getConfig();
        require_once($config['CliRunnerFile']);
        $className = $config['CliRunnerClass'];
        $args = getenv('KMSCI_RUNNER_ARGS');
        $args = empty($args) ? array() : json_decode($args, true);
        return new $className($config, $args, $configPath);
    } else {
        return false;
    }
};

/**POST_CODE**/

return $container;