<?php
namespace Application\Controller;

use Application\Model\Client;
use Application\Model\LoginUser;
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

        LoginUser::ifLogged(function () {
            return Client::where([
                Arrays::filterByAllowedKeys($this->params, Client::getFieldsWithoutPrimaryKey())
            ])->fetchAll();
        });
    }

    public function createClient()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            Client::create(
                Arrays::filterByAllowedKeys($this->params, Client::getFieldsWithoutPrimaryKey())
            );
        });
    }

    public function updateClient()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            $client = Client::findById($this->params['id']);
            $client->updateAttributes(
                Arrays::filterByAllowedKeys($this->params, Client::getFieldsWithoutPrimaryKey())
            );
        });
    }
}
