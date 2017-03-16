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
        $output = fopen($path, "w");
        fwrite($output, $data);
        fclose($output);
    }
}
