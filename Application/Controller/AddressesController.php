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
                Arrays::filterByAllowedKeys($this->params, Address::getFieldsWithoutPrimaryKey())
            );
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
