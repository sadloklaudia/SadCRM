<?php
namespace Application\Model;

use Ouzo\Utilities\Path;

class FileLogger extends OnScreenLogger
{
    protected function log($context, $message)
    {
        $logFilename = Path::join(ROOT_PATH, 'logs', 'logs.txt');
        $this->writeToFile($logFilename, "[$context] $message");
    }

    protected function writeToFile($path, $data)
    {
        if (!file_exists($path)) {
            file_put_contents($path, "");
        }
        $output = fopen($path, "a");
        fwrite($output, $data . PHP_EOL);
        fclose($output);
    }
}
