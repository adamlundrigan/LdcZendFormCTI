<?php
namespace ExampleModule\InputFilter\Contact;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailingAddressInputFilterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $inputFilter = new MailingAddressInputFilter();
        
        return $inputFilter;
    }
}