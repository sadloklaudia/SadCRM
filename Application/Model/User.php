<?php
namespace Application\Model;

use JsonSerializable;
use Ouzo\Model;

/**
 * @property string name
 * @property string surname
 * @property string type
 * @property string login
 * @property string password
 * @property string created
 * @property string salt
 */
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
            'fields' => ['name', 'surname', 'type', 'login', 'password', 'created', 'salt']]);
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
