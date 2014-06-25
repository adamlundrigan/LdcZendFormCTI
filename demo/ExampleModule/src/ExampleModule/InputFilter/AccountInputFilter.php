<?php
namespace ExampleModule\InputFilter;

use Zend\InputFilter\InputFilter;
use LdcZendFormCTI\InputFilter\NonuniformCollectionInputFilter;

class AccountInputFilter extends InputFilter
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
            'name'       => 'displayName',
            'required'   => true,
            'validators' => array(),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));        
        
        $this->add(array(
            'name'       => 'username',
            'required'   => true,
            'validators' => array(
                array('name' => 'Alnum'),
            ),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
        
        $coll = new NonuniformCollectionInputFilter();
        $coll->setDiscriminatorFieldName('type');
        $this->add($coll, 'contacts');
    }
}