<?php
namespace ExampleModule\Form\Contact;

use Zend\Form\Fieldset;

class MailingAddressFieldset extends AbstractContactFieldset
{
    public function __construct($name = null, $options = array()) 
    {
        parent::__construct($name, $options);
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'lineOne',
            'options' => array(
                'label' => 'Line One',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'lineTwo',
            'options' => array(
                'label' => 'Line Two',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'city',
            'options' => array(
                'label' => 'City',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'state',
            'options' => array(
                'label' => 'State',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'country',
            'options' => array(
                'label' => 'Country',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'postalCode',
            'options' => array(
                'label' => 'Postal Code',
            ),
        ));
    }
}