<?php
namespace Application\Controller;

use Ouzo\Controller;
use Ouzo\I18n;

class HelloWorldController extends Controller
{
    public function index()
    {
        $this->layout->renderAjax(json_encode([
            'siema' => I18n::t('sadcrm.hello')
        ]));
    }
}