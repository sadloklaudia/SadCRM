<?php
namespace Application\Controller;

use Ouzo\Controller;
use Ouzo\Logger\Logger;

class TestsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    public function test()
    {
        Logger::getLogger(__CLASS__)->debug("Siema");
    }
}
