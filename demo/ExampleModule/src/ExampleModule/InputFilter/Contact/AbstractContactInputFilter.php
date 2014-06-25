<?php
namespace ExampleModule\InputFilter\Contact;

use Zend\InputFilter\InputFilter;
use LdcZendFormCTI\InputFilter\NonuniformCollectionInputFilter;

class AbstractContactInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'id',
            'required'   => false,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'Digits'),
            ),
        ));
        
        $this->add(array(
            'name'       => 'accountId',
            'required'   => false,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'Digits'),
            ),
        ));        
        
        $this->add(array(
            'name'       => 'type',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
    }
}