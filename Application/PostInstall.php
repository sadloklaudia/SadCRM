<?php
namespace Application;

use Ouzo\Utilities\Path;

class PostInstall
{
    public static function createLogFolder()
    {
        $rootPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        $directory = Path::join($rootPath, 'logs');
        if (is_dir($directory)) {
            echo "Directory $directory already exists";
        } else {
            mkdir($directory);
            echo "Created $directory.";
        }
    }
}
