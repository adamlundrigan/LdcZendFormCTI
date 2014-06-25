<?php
namespace ExampleModule\Entity;

class AccountEntity
{
    protected $id;
    
    protected $displayName;
    
    protected $username;
    
    protected $contacts = array();
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function setDisplayName($dn) {
        $this->displayName = $dn;
    }
    
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getContacts() {
        return $this->contacts;
    }

    public function setContacts($contacts) {
        $this->contacts = array();
        $this->addContacts($contacts);
    }
    
    public function addContacts($contacts) {
        foreach ((array)$contacts as $contact) {
            $this->addContact($contact);
        }
    }
    
    public function addContact($contact) {
        $contact->setAccountId($this->getId());
        $this->contacts[] = $contact;
    }
    
    public function removeContacts($contacts) {
        foreach ((array)$contacts as $contact) {
            $this->removeContact($contact);
        }
    }
    
    public function removeContact($toBeRemoved)
    {
        foreach ( $this->contacts as $key => $contact ) {
            $diff = array_diff_assoc($contact, $toBeRemoved);
            if ( empty($diff) ) {
                $toBeRemoved->setAccountId(NULL);
                unset($this->contacts[$key]);
            }
        }
    }

}