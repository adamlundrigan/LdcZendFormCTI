<?php
namespace LdcZendFormCTITest\Form\Element\TestAssets\Fieldset;

use Zend\Form\Fieldset;

class RoleCFieldset extends Fieldset
{
    public function __construct()
    {
        parent::__construct('role');

        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'type',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'c',
            'options' => array(
                'label' => 'Role C\'s Content',
            ),
        ));
    }
}
