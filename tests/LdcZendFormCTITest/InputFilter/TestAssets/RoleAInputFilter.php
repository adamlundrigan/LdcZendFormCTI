<?php
namespace LdcZendFormCTITest\InputFilter\TestAssets;

use Zend\InputFilter\InputFilter;

class RoleAInputFilter extends InputFilter
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
            'name'       => 'type',
            'required'   => true,
            'validators' => array(
                array(
                    'name' => 'InArray',
                    'options' => array(
                        'haystack' => array('a')
                    ),
                ),
            ),
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name'       => 'a',
            'required'   => true,
            'validators' => array(),
            'filters'    => array(
                array('name' => 'StringTrim'),
            ),
        ));
    }
}
