<?php
namespace Application\Model;

use Ouzo\Logger\Logger;
use Ouzo\Utilities\Functions;

class OutputBuffer
{
    public static function logOutput($name, callable $callback)
    {
        ob_start();
        $result = Functions::call($callback, null);
        $logs = ob_get_clean();
        Logger::getLogger(__CLASS__)->info("Invoking method $name.\n$logs");
        return $result;
    }
}
