<?php
namespace ExampleModule\Form\Contact;

use Zend\Form\Fieldset;

abstract class AbstractContactFieldset extends Fieldset
{
    public function __construct($name = null, $options = array()) 
    {
        parent::__construct($name, $options);
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => array(
                'label' => 'Identifier',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'accountId',
            'options' => array(
                'label' => 'Account ID',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'type',
            'options' => array(
                'label' => 'Record Type',
            ),
        )); 
    }
}