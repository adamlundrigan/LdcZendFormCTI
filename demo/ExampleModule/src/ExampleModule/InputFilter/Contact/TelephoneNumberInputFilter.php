<?php
namespace ExampleModule\InputFilter\Contact;

class TelephoneNumberInputFilter extends AbstractContactInputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'telephoneNumber',
            'required'   => true,
            'validators' => array(
                array('name' => 'PhoneNumber'),
            ),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
    }
}