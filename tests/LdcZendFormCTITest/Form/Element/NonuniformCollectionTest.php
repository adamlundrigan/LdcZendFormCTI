<?php
namespace LdcZendFormCTITest\Form\Element;

use LdcZendFormCTITest\TestCase;

class NonuniformCollectionTest extends TestCase
{

    public function testBasicSmokeTestForDataExtractionFromPrebuiltEntityStructure()
    {
        $someFakeData = $this->getTestingDataset();

        $obj = $this->getTestingEntity();

        $form = $this->getTestingForm();
        $form->bind($obj);
        $form->isValid();

        $extractedData = $form->getData(\Zend\Form\FormInterface::VALUES_AS_ARRAY);
        $this->assertEquals($someFakeData, $extractedData);
    }

    public function testBasicSmokeTestForEntityHydrationFromAlreadyPreprocessedFormData()
    {
        $form = $this->getTestingForm();

        $someFakeData = $this->getTestingDataset();

        // Push in data, pull out entity...it's like magic!
        $form->setData($someFakeData);
        $this->assertTrue($form->isValid());
        $entity = $form->getData();

        // Validate the structure of the hydrated entity

        $this->assertInstanceOf('LdcZendFormCTITest\Form\Element\TestAssets\Entity\AccountEntity', $entity);
        $this->assertEquals($someFakeData['account']['id'], $entity->getId());

        $roles = $entity->getRoles();
        $this->assertInternalType('array', $roles);
        $this->assertCount(3, $roles);
        $this->assertInstanceOf('LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity', $roles[0]);
        $this->assertEquals($someFakeData['account']['roles'][0]['id'], $roles[0]->getId());
        $this->assertEquals($someFakeData['account']['roles'][0]['b'], $roles[0]->getB());
        $this->assertInstanceOf('LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleAEntity', $roles[1]);
        $this->assertEquals($someFakeData['account']['roles'][1]['id'], $roles[1]->getId());
        $this->assertEquals($someFakeData['account']['roles'][1]['a'], $roles[1]->getA());
        $this->assertInstanceOf('LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity', $roles[2]);
        $this->assertEquals($someFakeData['account']['roles'][2]['id'], $roles[2]->getId());
        $this->assertEquals($someFakeData['account']['roles'][2]['b'], $roles[2]->getB());
    }

    /**
     * @dataProvider providerTestTemplateElementsArePreparedCorrectly
     *
     * @param bool   $shouldPrepareTemplate value passed to setShouldCreateTemplate
     * @param string $expectedName          expected name of the template fieldsets
     */
    public function testTemplateElementsArePreparedCorrectly($shouldPrepareTemplate, $expectedName)
    {
        $form = $this->getTestingForm();
        $form->prepare();

        $templates = $form->get('account')->get('roles')->getTemplateElement();
        foreach ($templates as $objTemplate) {
            $this->assertEquals($expectedName, $objTemplate->getName());
        }
    }

    public function providerTestTemplateElementsArePreparedCorrectly()
    {
        return array(
            array( true, 'account[roles][__index__]' ),
            array( false, '__index__' ),
        );
    }

}
