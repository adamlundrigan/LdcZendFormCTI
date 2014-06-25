<?php
namespace ExampleModule\InputFilter\Contact;

class EmailAddressInputFilter extends AbstractContactInputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'emailAddress',
            'required'   => true,
            'validators' => array(
                array('name' => 'EmailAddress'),
            ),
            'filters'   => array(
                array('name' => 'StringTrim'),
            ),
        ));
    }
}