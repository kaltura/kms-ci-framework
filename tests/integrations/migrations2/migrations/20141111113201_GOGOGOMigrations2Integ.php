<?php


class GOGOGOMigrations2Integ extends KmsCi_Kmig_Migration
{

    

    public function up()
    {
        $this->_migrator()->entry->add('testimage123')->addContentFromFile($this->_integration()->getIntegrationFilename('test.png'))->commit();
    }

    public function down()
    {
        $this->_autoMigrateDown();
    }

}
