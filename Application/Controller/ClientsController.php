<?php
namespace Application\Controller;

use Application\Model\Client;
use Application\Model\LoginUser;
use Application\Model\User;
use Ouzo\Controller;
use Ouzo\Utilities\Arrays;

class ClientsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    public function find()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function (User $user) {
            return Client::where([
                Arrays::filterByAllowedKeys($this->params, ['pesel', 'surname']),
                'user_id' => $user->getId()
            ])->fetchAll();
        });
    }
}
