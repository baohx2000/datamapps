<?php
namespace MapperApp\Entities;


use Synapse\Entity\AbstractEntity;

class AddressEntity extends AbstractEntity
{
    protected $object = [
        'id' => null,
        'person_id' => null,
        'created' => null,
        'modified' => null,
        'line1' => null,
        'line2' => null,
        'city' => null,
        'state' => null,
        'postal_code' => null,
    ];
}
