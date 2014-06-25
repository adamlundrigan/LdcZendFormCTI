<?php
namespace ExampleModule\Form;

use Zend\Form\Fieldset;

class AccountFieldset extends Fieldset
{
    public function __construct($name = null, $options = array()) 
    {
        parent::__construct($name, $options);
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => array(
                'label' => 'Identifier',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'displayName',
            'options' => array(
                'label' => 'Display Name',
            ),
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'username',
            'options' => array(
                'label' => 'Username',
            ),
        ));
        
        $this->add(array(
            'type' => 'LdcZendFormCTI\Form\Element\NonuniformCollection',
            'name' => 'contacts',
            'options' => array(
                'label' => 'Contacts',
                'discriminator_field_name' => 'type',
                'should_create_template' => true,
                'target_element' => array(
                    // Added in factory
                ),
            ),
        ));
    }
}