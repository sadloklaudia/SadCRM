<?php
namespace Application\Controller;

use Ouzo\Controller;
use Ouzo\Logger\Logger;

class TestsController extends Controller
{
    public function test()
    {
        $logger = Logger::getLogger(__CLASS__);
        $logger->debug("Siema");
    }
}
