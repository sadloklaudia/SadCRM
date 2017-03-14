<?php
namespace Application\Controller;

use Application\Model\Client;
use Ouzo\Controller;
use Ouzo\Utilities\Json;

class ClientsController extends Controller
{
    public function findByPesel()
    {
        $pesel = $this->params['pesel'];

        $clients = Client::where(['pesel' => $pesel])->fetchAll();

        echo Json::encode([
            'hej' => [5, 7, 8, 56]
//            'clients' => $clients
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