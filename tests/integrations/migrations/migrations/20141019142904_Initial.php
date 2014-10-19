<?php


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
