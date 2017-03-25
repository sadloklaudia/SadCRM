<?php
namespace Application\Controller;

use Application\Model\Client;
use Application\Model\LoginUser;
use Ouzo\Controller;
use Ouzo\Restrictions;
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
            $query = Client::where([
                Arrays::filterByAllowedKeys($this->params, Client::getFieldsWithoutPrimaryKey())
            ]);
            if (Arrays::getValue($this->params, 'has_mail')) {
                $query->where(['mail' => Restrictions::notEqualTo('')]);
            }
            if (Arrays::getValue($this->params, 'has_sellChance')) {
                $query->where(['sellChance' => Restrictions::isNotNull()]);
            }
            if (Arrays::getValue($this->params, 'telDateSoonerThan')) {
                $query->where(['telDate' => Restrictions::greaterOrEqualTo($this->params['telDateSoonerThan'])]);
            }
            return [
                'clients' => $query->fetchAll()
            ];
        });
    }

    public function findById()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            return ['client' => Client::findById($this->params['id'])];
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
