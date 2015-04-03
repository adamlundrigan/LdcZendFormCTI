<?php

namespace LdcZendFormCTITest\Form\Element\TestAssets\Fieldset;

use Zend\Form\Fieldset;

class AccountFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('account');

        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
            'options' => array(
                'label' => 'Account ID',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'name',
            'options' => array(
                'label' => 'Account Name',
            ),
        ));
    }
}
