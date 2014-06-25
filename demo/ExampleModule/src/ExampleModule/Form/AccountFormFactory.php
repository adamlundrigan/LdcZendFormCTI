<?php
namespace ExampleModule\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ExampleModule\Entity\AccountEntity;
use Zend\Stdlib\Hydrator\ClassMethods;

class AccountFormFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $form = new AccountForm(
            $sm->get('ExampleModule\Form\AccountFieldset'),
            $sm->get('ExampleModule\InputFilter\AccountInputFilter')
        );
        $form->setHydrator(new ClassMethods(false));
        $form->setObject(new AccountEntity());
        
        return $form;
    }
}