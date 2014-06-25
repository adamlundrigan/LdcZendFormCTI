<?php
namespace ExampleModule\Entity\Contact;

class TelephoneNumberEntity extends AbstractContactEntity
{
    protected $telephoneNumber;

    public function getTelephoneNumber() {
        return $this->telephoneNumber;
    }

    public function setTelephoneNumber($telephoneNumber) {
        $this->telephoneNumber = $telephoneNumber;
        return $this;
    }
    
    public function getType() {
        return 'tel'; 
    }

}