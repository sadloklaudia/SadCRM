<?php

class AddAddresses extends Ruckusing_Migration_Base
{
    public function up()
    {
        $table = $this->create_table('addresses');

        $table->column('street', 'string');
        $table->column('number', 'string');
        $table->column('city', 'string');
        $table->column('postCode', 'string');

        $table->finish();
    }

    public function down()
    {
        $this->drop_table('addresses');
    }
}
