<?php
namespace ExampleModule\Form\Contact;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use ExampleModule\Entity\Contact\MailingAddressEntity;

class MailingAddressFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $fieldset = new MailingAddressFieldset();
        $fieldset->setName('mailaddr');
        $fieldset->setHydrator(new ClassMethods(false));
        $fieldset->setObject(new MailingAddressEntity());
        
        return $fieldset;
    }
}