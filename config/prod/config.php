<?php
$config['db']['dbname'] = 'SadCRM';
$config['db']['user'] = 'root';
$config['db']['pass'] = '';
$config['db']['driver'] = 'mysql';
$config['db']['host'] = '127.0.0.1';
$config['db']['port'] = '3306';

$config['global']['prefix_system'] = '/SadCRM';
$config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\MySqlDialect';
$config['debug'] = true;

return $config;
