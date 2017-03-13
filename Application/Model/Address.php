<?php
namespace Application\Model;

use Ouzo\Model;

class Address extends Model
{
    function __construct(array $attributes = [])
    {
        parent::__construct([
            'attributes' => $attributes,
            'hasMany' => [
                'client' => [
                    'class' => 'Client', 'foreignKey' => 'address_id'
                ]
            ],
            'fields' => ["street", "number", "city", "postCode"]]);
    }

    public function validate()
    {
        parent::validate();
        $this->validateNotBlank($this->login, 'Login cannot be blank');
    }
}