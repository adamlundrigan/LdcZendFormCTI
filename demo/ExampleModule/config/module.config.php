<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'example-module_controller_index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'example-module' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/:action',
                    'defaults' => array(
                        'controller' => 'example-module_controller_index',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'example-module_controller_index' => 'ExampleModule\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    
    'service_manager' => array(
        'aliases' => array(),
        'invokables' => array(),
        'factories' => array(
            'ExampleModule\Form\AccountForm' => 'ExampleModule\Form\AccountFormFactory',
            
            'ExampleModule\Form\AccountFieldset' => 'ExampleModule\Form\AccountFieldsetFactory',
            'ExampleModule\Form\Contact\EmailAddressFieldset' => 'ExampleModule\Form\Contact\EmailAddressFieldsetFactory',
            'ExampleModule\Form\Contact\MailingAddressFieldset' => 'ExampleModule\Form\Contact\MailingAddressFieldsetFactory',
            'ExampleModule\Form\Contact\TelephoneNumberFieldset' => 'ExampleModule\Form\Contact\TelephoneNumberFieldsetFactory',
            
            'ExampleModule\InputFilter\AccountInputFilter' => 'ExampleModule\InputFilter\AccountInputFilterFactory',
            'ExampleModule\InputFilter\Contact\EmailAddressInputFilter' => 'ExampleModule\InputFilter\Contact\EmailAddressInputFilterFactory',
            'ExampleModule\InputFilter\Contact\MailingAddressInputFilter' => 'ExampleModule\InputFilter\Contact\MailingAddressInputFilterFactory',
            'ExampleModule\InputFilter\Contact\TelephoneNumberInputFilter' => 'ExampleModule\InputFilter\Contact\TelephoneNumberInputFilterFactory',
        ),
    ),
);
