<?php
namespace LdcZendFormCTITest\InputFilter\TestAssets;

use Zend\InputFilter\BaseInputFilter;

class AccountInputFilter extends BaseInputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'id',
            'required'   => false,
            'validators' => array(),
            'filters'    => array(
                array('name' => 'Digits'),
            ),
        ));

        $this->add(array(
            'name'       => 'name',
            'required'   => true,
            'validators' => array(),
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
        ));
    }
}
