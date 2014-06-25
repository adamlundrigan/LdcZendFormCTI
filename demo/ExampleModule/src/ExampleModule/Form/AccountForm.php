<?php
namespace ExampleModule\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use ExampleModule\InputFilter\AccountInputFilter;

class AccountForm extends Form
{
    public function __construct(AccountFieldset $fsAccount, AccountInputFilter $ifAccount)
    {
        parent::__construct();
        
        $fsAccount->setUseAsBaseFieldset(true);
        $this->add($fsAccount);
        
        $if = new InputFilter();
        $if->add($ifAccount, 'account');
        $this->setInputFilter($if);
        
        // Add the submit button
        $this->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'options' => array(
                'label' => 'Save',
            ),
        ));
    }
}