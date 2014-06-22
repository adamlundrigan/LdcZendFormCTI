<?php
namespace LdcZendFormCTITest\Form\Element;

use LdcZendFormCTITest\TestCase;
use LdcZendFormCTI\Form\Element\NonuniformCollection;

class NonuniformCollectionTest extends TestCase
{

    public function testSetDiscriminatorFieldNameOption()
    {
        $obj = new NonuniformCollection('test', array(
            'discriminator_field_name' => 'foobar',
        ));
        $this->assertEquals('foobar', $obj->getDiscriminatorFieldName());
    }

    public function testSetTargetElementRejectsNonTraversableArgument()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');

        $obj = new NonuniformCollection();
        $obj->setTargetElement('test');
    }

    public function testSetTargetElementDefersToFormFactoryToCreateElementFromNonElementArgument()
    {
        $targetElement = array('name' => 'foobar');

        $mockElement = \Mockery::mock('Zend\Form\ElementInterface');

        $mockFormManager = \Mockery::mock('Zend\Form\Factory');
        $mockFormManager->shouldReceive('create')->withArgs(array($targetElement))->once()->andReturn($mockElement);

        $obj = \Mockery::mock('LdcZendFormCTI\Form\Element\NonuniformCollection[getFormFactory]');
        $obj->shouldReceive('getFormFactory')->once()->andReturn($mockFormManager);

        $obj->setTargetElement(array('test' => $targetElement));
        $this->assertEquals(array('test' => $mockElement), $obj->getTargetElement());
    }

    public function testSetTargetElementWillThrowExceptionWhenFormFactoryCannotCreateElement()
    {
        $targetElement = array('name' => 'foobar');

        $mockFormManager = \Mockery::mock('Zend\Form\Factory');
        $mockFormManager->shouldReceive('create')->withArgs(array($targetElement))->once()->andReturnNull();

        $obj = \Mockery::mock('LdcZendFormCTI\Form\Element\NonuniformCollection[getFormFactory]');
        $obj->shouldReceive('getFormFactory')->once()->andReturn($mockFormManager);

        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $obj->setTargetElement(array('test' => $targetElement));
    }

    public function testSetTargetElementWillThrowExceptionWhenSuppliedElementIsInvalid()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');

        $obj = new NonuniformCollection();
        $obj->setTargetElement(array('test' => 'notgonnawork'));
    }

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

    public function testTemplateElementsArePreparedCorrectlyWhenAsked()
    {
        $form = $this->getTestingForm();

        $element = $form->get('account')->get('roles');
        $element->setShouldCreateTemplate(true);

        $form->prepare();

        $templates = $element->getTemplateElement();
        $this->assertNotEmpty($templates);

        foreach ($templates as $objTemplate) {
            $this->assertEquals('__index__', $objTemplate->getName());
        }
    }

}
