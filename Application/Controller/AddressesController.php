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

    public function findById()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            return ['address' => Address::findById($this->params['id'])];
        });
    }

    public function createAddress()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            $address = Address::create(
                Arrays::filterByAllowedKeys($this->params, Address::getFieldsWithoutPrimaryKey())
            );
            return ['id' => $address->getId()];
        });
    }

    public function updateAddress()
    {
        LoginUser::login($this->params);

        LoginUser::ifLogged(function () {
            $address = Address::findById($this->params['id']);
            $address->updateAttributes(
                Arrays::filterByAllowedKeys($this->params, Address::getFieldsWithoutPrimaryKey())
            );
        });
    }
}
