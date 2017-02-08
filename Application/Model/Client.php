<?php
namespace Application\Model;

use JsonSerializable;
use Ouzo\Model;

class Client extends Model implements JsonSerializable
{
    function __construct(array $attributes = [])
    {
        parent::__construct([
            'attributes' => $attributes,
            'belongsTo' => [
                'user' => ['class' => 'User', 'foreignKey' => 'user_id'],
                'address' => ['class' => 'Address', 'foreignKey' => 'address_id'],
            ],
            'fields' => ['address_id', 'user_id', 'name', 'surname', 'pesel', 'phone1', 'phone2',
                'mail', 'description', 'vip', 'created', 'products', 'sellChance', 'modified', 'tel', 'telDate']]);
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
