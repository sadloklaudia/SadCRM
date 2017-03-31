<?php
namespace Application\Controller;

use Application\Model\Application;
use Application\Model\EncryptedMessages;
use Application\Model\LoginUser;
use Application\Model\Password;
use Application\Model\SaltGenerator;
use Application\Model\User;
use Ouzo\Controller;
use Ouzo\Utilities\Arrays;

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
                'version' => Application::VERSION,
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

    public function find()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            return [
                'users' => User::where([
                    Arrays::filterByAllowedKeys($this->params, User::getFieldsWithoutPrimaryKey())
                ])->fetchAll()
            ];
        });
    }

    public function findById()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            return ['user' => User::findById($this->params['id'])];
        });
    }

    public function createUser()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            User::create(
                Arrays::filterByAllowedKeys(
                    $this->saltifyUserPassword($this->params),
                    User::getFieldsWithoutPrimaryKey()
                )
            );
        });
    }

    public function updateUser()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            $user = User::findById($this->params['id']);
            $user->updateAttributes(
                Arrays::filterByAllowedKeys(
                    $this->saltifyUserPassword($this->params),
                    User::getFieldsWithoutPrimaryKey()
                )
            );
        });
    }

    public function changePassword()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function (User $user) {
            $user->updateAttributes([
                'password' => Password::hash($this->params['new_password'], $user->salt)
            ]);
        });
    }

    private function saltifyUserPassword($params)
    {
        $salt = SaltGenerator::getSalt();
        $params['salt'] = $salt;
        $params['password'] = Password::hash($params['password'], $salt);
        return $params;
    }
}