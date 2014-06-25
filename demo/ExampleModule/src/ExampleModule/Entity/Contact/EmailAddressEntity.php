<?php
namespace ExampleModule\Entity\Contact;

class EmailAddressEntity extends AbstractContactEntity
{
    protected $emailAddress;
    
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
        return $this;
    }
    
    public function getType() {
        return 'email'; 
    }

}