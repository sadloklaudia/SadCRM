<?php
namespace Application\Controller;

use Application\Model\Application;
use Ouzo\Controller;
use Ouzo\Utilities\Json;

class TestsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    public function test()
    {
        echo Json::encode([
            'success' => true,
            'version' => Application::VERSION
        ]);
    }
}
