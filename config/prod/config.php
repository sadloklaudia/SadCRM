<?php
$config['db']['dbname'] = 'u866879676_sad';
$config['db']['user'] = 'u866879676_root';
$config['db']['pass'] = '76a5ba50d31';
$config['db']['driver'] = 'mysql';
$config['db']['host'] = 'mysql.hostinger.pl';
$config['db']['port'] = '3306';

$config['global']['prefix_system'] = '/SadCRM';
$config['sql_dialect'] = '\\Ouzo\\Db\\Dialect\\MySqlDialect';
$config['debug'] = true;

$config['logger']['default']['class'] = 'Application\Model\FileLogger';

return $config;
