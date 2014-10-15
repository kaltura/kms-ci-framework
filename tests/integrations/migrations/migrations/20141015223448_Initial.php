<?php
// change this path to your kaltura lib or to point to your relative vendor dir
require_once(__DIR__.'/../../../../vendor/kaltura/kmig/lib/Kaltura/autoload.php');

class Initial extends \Kmig\Helper\Phpmig\KmigMigration
{

    

    public function up()
    {
        $this->_migrator()->entry->add('test123')->commit();
    }

    public function down()
    {
        $this->_autoMigrateDown();
    }

}
