<?php
/**
 * Created by PhpStorm.
 * User: gordon
 * Date: 4/19/15
 * Time: 7:47 PM
 */

namespace ORMApp\Entities;

use ORMApp\Helper\StampEntityTrait;

/** @Entity
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="person")
 */
class Person {
    use StampEntityTrait;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $firstName;

    private $lastName;


}