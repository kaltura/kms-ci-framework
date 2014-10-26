<?php

// change this to point to your vendor autoload file
// or do any other autoloading you require in your migrations
require_once(__DIR__.'/../../../vendor/autoload.php');

// also, remember to autoload your kaltura library - you can use kmig's library if you want
require_once(__DIR__.'/../../../vendor/kaltura/kmig/lib/Kaltura/autoload.php');

$container = KmsCi_Kmig_IntegrationHelper::getInstanceByIntegrationId('migrations')->bootstrapContainer();

return $container;
