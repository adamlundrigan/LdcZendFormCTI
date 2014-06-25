<?php
namespace ExampleModule\Form\Contact;

use Zend\Form\Fieldset;

class EmailAddressFieldset extends AbstractContactFieldset
{
    public function __construct($name = null, $options = array()) 
    {
        parent::__construct($name, $options);
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'emailAddress',
            'options' => array(
                'label' => 'Email Address',
            ),
        ));
        
    }
}