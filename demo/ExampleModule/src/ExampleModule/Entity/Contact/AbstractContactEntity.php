<?php
namespace ExampleModule\Entity\Contact;

abstract class AbstractContactEntity
{
    protected $id;
    
    protected $accountId;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getAccountId() {
        return $this->accountId;
    }

    public function setAccountId($accountId) {
        $this->accountId = $accountId;
        return $this;
    }
    
    abstract function getType();

}
