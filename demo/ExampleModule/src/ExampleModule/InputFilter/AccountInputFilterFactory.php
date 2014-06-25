<?php
namespace ExampleModule\InputFilter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountInputFilterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $inputFilter = new AccountInputFilter();
        
        $inputFilter->get('contacts')->setInputFilter(array(
            'email'     => $sm->get('ExampleModule\InputFilter\Contact\EmailAddressInputFilter'),
            'mailaddr'  => $sm->get('ExampleModule\InputFilter\Contact\MailingAddressInputFilter'),
            'telephone' => $sm->get('ExampleModule\InputFilter\Contact\TelephoneNumberInputFilter'),
        ));
        
        return $inputFilter;
    }
}