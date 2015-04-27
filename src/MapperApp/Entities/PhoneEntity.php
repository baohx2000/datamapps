<?php
namespace MapperApp\Entities;


use Synapse\Entity\AbstractEntity;

class PhoneEntity extends AbstractEntity
{
    protected $object = [
        'id' => null,
        'person_id' => null,
        'phone_number' => null,
        'created' => null,
        'modified' => null,
    ];
}
