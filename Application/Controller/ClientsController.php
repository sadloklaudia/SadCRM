<?php
namespace Application\Controller;

use Application\Model\Client;
use Ouzo\Controller;

class ClientsController extends Controller
{
    public function findByPesel()
    {
        $pesel = $this->params['pesel'];

        $clients = Client::where(['pesel' => $pesel])->fetchAll();

        echo json_encode([
            'clients' => $clients
        ]);
    }

    public function findBySurname()
    {
        $surname = $this->params['surname'];

        $clients = Client::where(['surname' => $surname])->fetchAll();

        echo json_encode([
            'clients' => $clients
        ]);
    }
}