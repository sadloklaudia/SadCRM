<?php
namespace Application\Model;

use JsonSerializable;
use Ouzo\Model;

class User extends Model implements JsonSerializable
{
    function __construct(array $attributes = [])
    {
        parent::__construct([
            'attributes' => $attributes,
            'hasMany' => [
                'client' => [
                    'class' => Client::class, 'foreignKey' => 'user_id'
                ]
            ],
            'fields' => ['name', "surname", "type", "login", "password", "created"]]);
    }

    public function validate()
    {
        parent::validate();
        $this->validateNotBlank($this->login, 'Login cannot be blank');
    }

    function jsonSerialize()
    {
        return $this->attributes();
    }
}
