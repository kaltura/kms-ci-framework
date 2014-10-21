<?php


class Runner extends KmsCi_Kmig_Migration
{

    

    public function up()
    {
        $this->_runner()->log('testing 1 2 3');
        $data = json_decode(file_get_contents(__DIR__.'/../tmpdata'), true);
        $data['runner'] = true;
        file_put_contents(__DIR__.'/../tmpdata', json_encode($data));
    }

    public function down()
    {
        $this->_runner()->log('[UNDO] testing 1 2 3');
    }

}
