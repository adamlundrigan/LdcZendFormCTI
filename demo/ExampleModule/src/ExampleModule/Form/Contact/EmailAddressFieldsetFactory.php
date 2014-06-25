<?php
namespace ExampleModule\Form\Contact;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use ExampleModule\Entity\Contact\EmailAddressEntity;

class EmailAddressFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $fieldset = new EmailAddressFieldset();
        $fieldset->setName('email');
        $fieldset->setHydrator(new ClassMethods(false));
        $fieldset->setObject(new EmailAddressEntity);
        
        return $fieldset;
    }
}