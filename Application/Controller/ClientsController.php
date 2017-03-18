<?php
namespace Application\Controller;

use Application\Model\Client;
use Ouzo\Controller;
use Ouzo\Utilities\Json;

class ClientsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    public function findByPesel()
    {
        $clients = Client::where([
            'pesel' => $this->params['pesel']
        ])->fetchAll();

        echo Json::encode([
            'clients' => $clients
        ]);
    }

    public function findBySurname()
    {
        $clients = Client::where([
            'surname' => $this->params['surname']
        ])->fetchAll();

        echo Json::encode([
            'clients' => $clients
        ]);
    }
}