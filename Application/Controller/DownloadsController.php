<?php
namespace Application\Controller;

use Ouzo\Config;
use Ouzo\Controller;

class DownloadsController extends Controller
{
    public function download()
    {
        $prefix = Config::getValue('prefix_system', 'prefix_system');
        header("Location: $prefix/SadCRMClient.jar");
    }
}
