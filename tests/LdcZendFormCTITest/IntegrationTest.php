<?php
namespace LdcZendFormCTITest;

class IntegrationTest extends TestCase
{
    public function testEntityToArrayHappyCase()
    {
        $entity = $this->getTestingEntity();

        $baseFilter = new \Zend\InputFilter\InputFilter();
        $accountFilter = new \Zend\InputFilter\InputFilter();
        $accountFilter->add($this->getTestingInputFilter(), 'roles');
        $baseFilter->add($accountFilter, 'account');

        $form = $this->getTestingForm();
        $form->setInputFilter($baseFilter);
        $form->bind($entity);
        $form->isValid();

        $extractedData = $form->getData(\Zend\Form\FormInterface::VALUES_AS_ARRAY);
        $this->assertEquals($this->getTestingDataset(), $extractedData);
    }

    public function testArrayToEntityHappyCase()
    {
        $baseFilter = new \Zend\InputFilter\InputFilter();
        $accountFilter = new \Zend\InputFilter\InputFilter();
        $accountFilter->add($this->getTestingInputFilter(), 'roles');
        $baseFilter->add($accountFilter, 'account');

        $form = $this->getTestingForm();
        $form->setInputFilter($baseFilter);
        $form->setData($this->getTestingDataset());
        $form->isValid();

        $extractedEntity = $form->getData();
        $this->assertEquals($this->getTestingEntity(), $extractedEntity);
    }
}
