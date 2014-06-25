<?php
namespace ExampleModule\Form\Contact;

use Zend\Form\Fieldset;

class TelephoneNumberFieldset extends AbstractContactFieldset
{
    public function __construct($name = null, $options = array()) 
    {
        parent::__construct($name, $options);
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'telephoneNumber',
            'options' => array(
                'label' => 'Tel #',
            ),
        ));
        
    }
}