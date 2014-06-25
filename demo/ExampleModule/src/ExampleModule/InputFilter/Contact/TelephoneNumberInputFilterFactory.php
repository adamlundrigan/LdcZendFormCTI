<?php
namespace ExampleModule\InputFilter\Contact;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TelephoneNumberInputFilterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $inputFilter = new TelephoneNumberInputFilter();
        
        return $inputFilter;
    }
}