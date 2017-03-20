<?php
namespace Application\Model;

use JsonSerializable;
use Ouzo\Model;

/**
 * @property string street
 * @property string number
 * @property string city
 * @property string postCode
 */
class Address extends Model implements JsonSerializable
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
        $this->validateNotBlank($this->street, 'Street cannot be blank');
        $this->validateNotBlank($this->number, 'Number cannot be blank');
    }

    function jsonSerialize()
    {
        return $this->attributes();
    }
}
