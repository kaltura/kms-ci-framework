<?php

// change this to point to your vendor autoload file
// or do any other autoloading you require in your migrations
require_once(__DIR__.'/../../../vendor/autoload.php');

// also, remember to autoload your kaltura library - you can use kmig's library if you want
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