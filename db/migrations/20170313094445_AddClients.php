<?php

class AddClients extends Ruckusing_Migration_Base
{
    public function up()
    {
        $clients = $this->create_table('clients');

        $clients->column('id', 'integer');
        $clients->column('address_id', 'integer');
        $clients->column('user_id', 'integer');
        $clients->column('name', 'string');
        $clients->column('surname', 'string');
        $clients->column('pesel', 'string');
        $clients->column('phone1', 'string');
        $clients->column('phone2', 'string');
        $clients->column('mail', 'string');
        $clients->column('description', 'string');
        $clients->column('vip', 'bool');
        $clients->column('created', 'datetime');
        $clients->column('products', 'string');
        $clients->column('sellChance', 'string');
        $clients->column('modified', 'datetime');
        $clients->column('tel', 'string');
        $clients->column('telDate', 'datetime');

        $clients->finish();
    }
}