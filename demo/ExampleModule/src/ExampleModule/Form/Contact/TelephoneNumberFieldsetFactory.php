<?php
namespace ExampleModule\Form\Contact;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use ExampleModule\Entity\Contact\TelephoneNumberEntity;

class TelephoneNumberFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $fieldset = new TelephoneNumberFieldset();
        $fieldset->setName('telephone');
        $fieldset->setHydrator(new ClassMethods(false));
        $fieldset->setObject(new TelephoneNumberEntity());
        
        return $fieldset;
    }
}