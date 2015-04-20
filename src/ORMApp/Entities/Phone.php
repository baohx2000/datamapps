<?php
namespace ORMApp\Entities;

use Doctrine\ORM\Mapping AS ORM;
use ORMApp\Helper\StampEntityTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="phones")
 */
class Phone {
    use StampEntityTrait;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $phoneNumber;

    /**
     * @var Person
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="phones")
     */
    private $person;

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

}
