<?php

class AddUsers extends Ruckusing_Migration_Base
{
    public function up()
    {
        $users = $this->create_table("users");

        $users->column("login", "string");
        $users->column("password", "string");
        $users->column('name', 'string');
        $users->column("surname", 'string');
        $users->column("type", 'string');
        $users->column("created", 'string');

        $users->finish();
    }
}
