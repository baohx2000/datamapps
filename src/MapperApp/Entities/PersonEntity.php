<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 4/25/15
 * Time: 6:43 PM
 */

namespace MapperApp\Entities;


use Synapse\Entity\AbstractEntity;

class PersonEntity extends AbstractEntity
{
    protected $object = [
        'id' => null,
        'first_name' => null,
        'last_name' => null,
        'created' => null,
        'modified' => null,
        'addresses' => [],
        'phones'    => [],
        'address_id' => null,
        'phone_id' => null,
        'address' => null,
    ];

    public function getColumns()
    {
        return array_diff(
            array_keys($this->object),
            [
                'address',
                'addresses',
                'phones',
                'address_id',
                'phone_id',
            ]
        );
    }
}
