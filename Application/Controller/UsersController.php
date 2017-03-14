<?php
namespace Application\Controller;

use Application\Model\User;
use Ouzo\Controller;
use Ouzo\Utilities\Json;

class UsersController extends Controller
{
    public function findByName()
    {
        $user = User::where([
            'name' => $this->params['name']
        ])->fetchAll();

        echo Json::encode(['users' => $user]);
    }

    public function findBySurname()
    {
        $user = User::where([
            'surname' => $this->params['surname']
        ])->fetchAll();

        echo Json::encode(['users' => $user]);
    }
}