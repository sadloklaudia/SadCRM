<?php
namespace Application\Controller;

use Application\Model\LoginUser;
use Application\Model\User;
use Application\Model\EncryptedMessages;
use Ouzo\Controller;
use Ouzo\Utilities\Json;

class UsersController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
        $this->params = EncryptedMessages::decryptMessage($this);
    }

    public function login()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function (User $user) {
            return [
                'user' => [
                    'id' => $user->getId(),
                    'login' => $user->login,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'type' => $user->type,
                    'created' => $user->created
                ]];
        });
    }

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

    public function changePassword()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function (User $user) {
            $user->updateAttributes([
                'password' => $this->params['new_password']
            ]);
        });
    }
}