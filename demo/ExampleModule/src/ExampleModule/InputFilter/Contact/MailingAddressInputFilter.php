<?php
namespace ExampleModule\InputFilter\Contact;

class MailingAddressInputFilter extends AbstractContactInputFilter
{
    public function __construct()
    {
        parent::__construct();
        
        $this->add(array(
            'name'       => 'lineOne',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
        
        $this->add(array(
            'name'       => 'lineTwo',
            'required'   => false,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
        
        $this->add(array(
            'name'       => 'city',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
        
        $this->add(array(
            'name'       => 'state',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
        
        $this->add(array(
            'name'       => 'country',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
        
        $this->add(array(
            'name'       => 'postalCode',
            'required'   => true,
            'validators' => array(
                array('name' => 'PostCode'),
            ),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
    }
}