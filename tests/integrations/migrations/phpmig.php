<?php
// change this path to your kaltura lib or to point to your relative vendor dir
require_once(__DIR__.'/../../../vendor/kaltura/kmig/lib/Kaltura/autoload.php');


$container = new \Kmig\Container(array(
    'Kmig_Migrator_ID' => 'kmsci_integration_migrations',
    'Kmig_Phpmig_Adapter_DataFile' => __DIR__. DIRECTORY_SEPARATOR . '.kmig.phpmig.data',
));

$container['phpmig.adapter'] = function($c) {
    return new \Kmig\Helper\Phpmig\KmigAdapter($c);
};

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';



return $container;