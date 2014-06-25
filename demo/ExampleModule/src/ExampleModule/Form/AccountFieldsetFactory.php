<?php
namespace ExampleModule\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountFieldsetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return Authentication
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        $fieldset = new AccountFieldset();
        $fieldset->setName('account');
        
        $fieldset->get('contacts')->setTargetElement(array(
            'email'     => $sm->get('ExampleModule\Form\Contact\EmailAddressFieldset'),
            'mailaddr'  => $sm->get('ExampleModule\Form\Contact\MailingAddressFieldset'),
            'telephone' => $sm->get('ExampleModule\Form\Contact\TelephoneNumberFieldset'),
        ));
        
        return $fieldset;
    }
}