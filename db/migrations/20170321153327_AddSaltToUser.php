<?php

class AddSaltToUser extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->add_column('users', 'salt', 'string');
    }

    public function down()
    {
        $this->remove_column('users', 'salt');
    }
}
