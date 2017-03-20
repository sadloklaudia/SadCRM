<?php
namespace Application\Controller;

use Application\Model\Address;
use Application\Model\LoginUser;
use Ouzo\Controller;
use Ouzo\Utilities\Arrays;

class AddressesController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    public function createAddress()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            Address::create(
                Arrays::filterByAllowedKeys($this->params, ['street', 'number', 'city', 'postCode'])
            );
        });
    }

    public function updateAddress()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            $id = $this->params['id'];
            $address = Address::findById($id);
            $address->updateAttributes(
                Arrays::filterByAllowedKeys($this->params, ['street', 'number', 'city', 'postCode'])
            );
        });
    }
}
