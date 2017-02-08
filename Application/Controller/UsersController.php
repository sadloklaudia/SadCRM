<?php
namespace Application\Controller;

use Application\Model\User;
use Ouzo\Controller;

class UsersController extends Controller
{
    public function findByName()
    {
        $name = $this->params['name'];

        $user = User::where(['name' => $name])->fetchAll();

        echo json_encode(['users' => $user]);
    }

    public function findBySurname()
    {
        $surname = $this->params['surname'];

        $user = User::where(['surname' => $surname])->fetchAll();

        echo json_encode(['users' => $user]);
    }
}