<?php
namespace ExampleModule\Entity\Contact;

class MailingAddressEntity extends AbstractContactEntity
{
    
    protected $lineOne;
    
    protected $lineTwo;
    
    protected $city;
    
    protected $state;
    
    protected $country;
    
    protected $postalCode;
    
    public function getLineOne() {
        return $this->lineOne;
    }

    public function setLineOne($lineOne) {
        $this->lineOne = $lineOne;
        return $this;
    }

    public function getLineTwo() {
        return $this->lineTwo;
    }

    public function setLineTwo($lineTwo) {
        $this->lineTwo = $lineTwo;
        return $this;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
        return $this;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
        return $this;
    }
    
    public function getType() {
        return 'mailaddr'; 
    }

}