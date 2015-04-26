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
     * @ORM\Column(type="string", name="phone_number")
     */
    private $phoneNumber;

    /**
     * @var Person
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
     * @return $this
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
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
     * @return $this
     */
    public function setPerson($person)
    {
        $this->person = $person;
        return $this;
    }

}
