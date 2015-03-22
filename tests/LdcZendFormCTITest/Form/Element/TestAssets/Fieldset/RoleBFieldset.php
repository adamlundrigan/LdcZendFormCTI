<?php
namespace LdcZendFormCTITest\Form\Element\TestAssets\Fieldset;

use Zend\Form\Fieldset;

class RoleBFieldset extends Fieldset
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
            'name' => 'b',
            'options' => array(
                'label' => 'Role B\'s Content',
            ),
        ));
    }
}
