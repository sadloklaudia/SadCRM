<?php
namespace Application;

use Exception;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class PostInstall
{
    public static function createLogFolder()
    {
        $rootPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        $directory = Path::join($rootPath, 'logs');
        if (is_dir($directory)) {
            echo "Directory $directory already exists.\n";
        } else {
            mkdir($directory);
            echo "Created $directory.\n";
        }
    }

    public static function createConfigDevFolder()
    {
        $rootPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

        $devConfigDirectory = Path::join($rootPath, 'config', 'dev');
        if (!is_dir($devConfigDirectory)) {
            mkdir($devConfigDirectory);
        }

        $prodConfig = Path::join($rootPath, 'config', 'prod', 'config.php');
        $devConfig = Path::join($rootPath, 'config', 'dev', 'config.php');
        if (!Files::exists($devConfig)) {
            $success = copy($prodConfig, $devConfig);
            if ($success) {
                echo "Created $devConfig file." . PHP_EOL;
            } else {
                throw new Exception("Could not copy config to config-dev environment");
            }
        }
    }
}
