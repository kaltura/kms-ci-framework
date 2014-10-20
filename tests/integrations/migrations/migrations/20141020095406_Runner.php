<?php


class Runner extends KmsCi_Kmig_Migration
{

    

    public function up()
    {
        $this->_runner()->log('testing 1 2 3');
    }

    public function down()
    {
        $this->_runner()->log('[UNDO] testing 1 2 3');
    }

}
