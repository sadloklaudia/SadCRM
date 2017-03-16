<?php
namespace Application\Model;

use Ouzo\Logger\LoggerInterface;

class OnScreenLogger implements LoggerInterface
{
    /** @var string */
    private $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function error($message, $params = null)
    {
        $this->log('Error', $message);
    }

    public function info($message, $params = null)
    {
        $this->log('Info', $message);
    }

    public function debug($message, $params = null)
    {
        $this->log('Debug', $message);
    }

    public function warning($message, $params = null)
    {
        $this->log('Warning', $message);
    }

    public function fatal($message, $params = null)
    {
        $this->log('Fatal', $message);
    }

    protected function log($context, $message)
    {
        echo "[$context] $message";
    }
}
