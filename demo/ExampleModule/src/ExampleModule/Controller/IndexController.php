<?php
namespace ExampleModule\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ExampleModule\Entity\AccountEntity;
use ExampleModule\Entity\Contact\EmailAddressEntity;
use ExampleModule\Entity\Contact\MailingAddressEntity;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        
    }
    
    public function createAction()
    {
        $form = $this->getServiceLocator()->get('ExampleModule\Form\AccountForm');
        $form->bind(new AccountEntity());
        
        if ( $this->getRequest()->isPost() ) {
            $data = $this->getRequest()->getPost()->toArray();
            $data['account']['id'] = mt_rand();
            $form->setData($data);
            if ( $form->isValid() ) {
                return new ViewModel(array(
                    'success' => true,
                    'entity'  => $form->getData(),
                    'form'    => $form,
                ));
            }
        }
        
        return new ViewModel(array(
            'form' => $form,
        ));
    }
    
    public function updateAction()
    {
        $account = new AccountEntity();
        $account->setId(42);
        $account->setDisplayName('Testy McTesterson');
        $account->setUsername('testymctesterson99');
        
        $contactEmail = new EmailAddressEntity();
        $contactEmail->setId(99);
        $contactEmail->setEmailAddress('testy@mctesterson.ca');
        $account->addContact($contactEmail);
        
        $contactEmail2 = new EmailAddressEntity();
        $contactEmail2->setId(98);
        $contactEmail2->setEmailAddress('foobar@bazbat.ca');
        $account->addContact($contactEmail2);
        
        $mailingAddress = new MailingAddressEntity();
        $mailingAddress->setId(97);
        $mailingAddress->setLineOne('123 Some Street');
        $mailingAddress->setLineTwo('');
        $mailingAddress->setCity('Testville');
        $mailingAddress->setState('NL');
        $mailingAddress->setCountry('CA');
        $mailingAddress->setPostalCode('A1A 1A1');
        $account->addContact($mailingAddress);
        
        $form = $this->getServiceLocator()->get('ExampleModule\Form\AccountForm');
        $form->bind($account);
        
        if ( $this->getRequest()->isPost() ) {
            $data = $this->getRequest()->getPost()->toArray();
            $form->setData($data);
            if ( $form->isValid() ) {
                return new ViewModel(array(
                    'success' => true,
                    'entity'  => $form->getData(),
                    'form'    => $form,
                ));
            }
        }
        
        return new ViewModel(array(
            'form' => $form,
        ));
    }
}