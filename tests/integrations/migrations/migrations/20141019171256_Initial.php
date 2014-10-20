<?php


class Initial extends KmsCi_Kmig_Migration
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
