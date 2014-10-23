<?php


class Initial extends KmsCi_Kmig_Migration
{

    

    public function up()
    {
        $data = json_decode(file_get_contents(__DIR__.'/../tmpdata'), true);
        if (!$data['FIRST!']) throw new Exception();
        $this->_migrator()->entry->add('test123')->commit();
        $data['initial'] = true;
        file_put_contents(__DIR__.'/../tmpdata', json_encode($data));
    }

    public function down()
    {
        $this->_autoMigrateDown();
    }

}
