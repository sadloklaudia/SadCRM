<?php
namespace Application\Controller;

use Ouzo\Controller;

class TestsController extends Controller
{
    public function test()
    {
        phpinfo();
    }
}