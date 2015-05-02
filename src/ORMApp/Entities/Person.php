<?php
namespace ORMApp\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ORMApp\Helper\StampEntityTrait;
use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="person")
 */
class Person {
    use StampEntityTrait;

    /**
     * @var string
     * @ORM\Column(type="string", name="first_name")
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", name="last_name")
     */
    private $lastName;

    /**
     * @var Address[]|Collection
     * @ORM\OneToMany(targetEntity="Address", mappedBy="person", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
     */
    private $addresses;

    /**
     * @var Phone[]|Collection
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="person", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EAGER")
     */
    private $phones;

    public function __construct()
    {
        $this->phones = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    /**
     * @return Address[]|Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param Address[] $addresses
     * @return $this
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
        return $this;
    }

    /**
     * @param Address $address
     * @return $this
     */
    public function addAddress(Address $address)
    {
        $address->setPerson($this);
        $this->addresses->add($address);
        return $this;
    }

    /**
     * @param Address $address
     * @return $this;
     */
    public function removeAddress(Address $address)
    {
        $address->setPerson(null);
        $this->addresses->remove($address);
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return Collection|Phone[]
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @param Collection|Phone[] $phones
     * @return $this
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
        return $this;
    }

    public function addPhone(Phone $phone)
    {
        $phone->setPerson($this);
        $this->phones->add($phone);
        return $this;
    }

    public function removePhone(Phone $phone)
    {
        $phone->setPerson(null);
        $this->phones->remove($phone);
        return $this;
    }
}
