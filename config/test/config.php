<?php
$config['db']['dbname'] = 'sadcrm_test';
$config['db']['user'] = 'ouzo';
$config['db']['pass'] = 'password';
$config['db']['driver'] = 'mysql';
$config['db']['host'] = '127.0.0.1';
$config['db']['port'] = '3306';
$config['global']['prefix_system'] = '/SadCRM';
$config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\MySqlDialect';

return $config;
