<?php

namespace LdcZendFormCTITest\Form\Element;

use LdcZendFormCTITest\TestCase;
use LdcZendFormCTI\Form\Element\NonuniformCollection;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleC\OptionOneFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleC\OptionTwoFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleC\OptionOneEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleC\OptionTwoEntity;
use Zend\Stdlib\Hydrator\ClassMethods;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleCFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleCEntity;
use Zend\Stdlib\ArrayUtils;

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
            $this->assertEquals('account[roles][__index__]', $objTemplate->getName());
        }
    }

    public function testTemplateElementHasDiscriminatorFieldSet()
    {
        $form = $this->getTestingForm();

        $element = $form->get('account')->get('roles');
        $element->setShouldCreateTemplate(true);

        $form->prepare();

        $templates = $element->getTemplateElement();
        $this->assertNotEmpty($templates);

        foreach ($templates as $discr => $objTemplate) {
            $this->assertEquals($discr, $objTemplate->get('type')->getValue());
        }
    }

    /**
     * @group GH-12
     */
    public function testNestedCollection()
    {
        $form = $this->getTestingForm();

        $fsOptionOne = new OptionOneFieldset();
        $fsOptionOne->setObject(new OptionOneEntity());
        $fsOptionOne->setHydrator(new ClassMethods(false));

        $fsOptionTwo = new OptionTwoFieldset();
        $fsOptionTwo->setObject(new OptionTwoEntity());
        $fsOptionTwo->setHydrator(new ClassMethods(false));

        $collOptions = new NonuniformCollection();
        $collOptions->setName('options');
        $collOptions->setDiscriminatorFieldName('type');
        $collOptions->setTargetElement(array(
            'one' => $fsOptionOne,
            'two' => $fsOptionTwo,
        ));

        $fsRoleC = new RoleCFieldset();
        $fsRoleC->setObject(new RoleCEntity());
        $fsRoleC->setHydrator(new ClassMethods(false));
        $fsRoleC->add($collOptions);

        $targetElement = $form->get('account')->get('roles')->getTargetElement();
        $targetElement['c'] = $fsRoleC;
        $form->get('account')->get('roles')->setTargetElement($targetElement);

        $someFakeData = ArrayUtils::merge(
            $this->getTestingDataset(),
            array(
                'account' => array(
                    'roles' => array(
                        3 => array(
                            'id' => 986,
                            'type' => 'c',
                            'c' => 'baz',
                            'options' => array(
                                array(
                                    'id' => '123456',
                                    'type' => 'one',
                                    'firstOption' => 'testtest',
                                ),
                                array(
                                    'id' => '123457',
                                    'type' => 'one',
                                    'firstOption' => 'foobar',
                                ),
                                array(
                                    'id' => '123458',
                                    'type' => 'two',
                                    'secondOption' => 'lastone',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            true
        );

        $obj = $this->getTestingEntity();

        $options[0] = new OptionOneEntity();
        $options[0]->setId($someFakeData['account']['roles'][3]['options'][0]['id']);
        $options[0]->setType($someFakeData['account']['roles'][3]['options'][0]['type']);
        $options[0]->setFirstOption($someFakeData['account']['roles'][3]['options'][0]['firstOption']);

        $options[1] = new OptionOneEntity();
        $options[1]->setId($someFakeData['account']['roles'][3]['options'][1]['id']);
        $options[1]->setType($someFakeData['account']['roles'][3]['options'][1]['type']);
        $options[1]->setFirstOption($someFakeData['account']['roles'][3]['options'][1]['firstOption']);

        $options[2] = new OptionTwoEntity();
        $options[2]->setId($someFakeData['account']['roles'][3]['options'][2]['id']);
        $options[2]->setType($someFakeData['account']['roles'][3]['options'][2]['type']);
        $options[2]->setSecondOption($someFakeData['account']['roles'][3]['options'][2]['secondOption']);

        $objRoleC = new RoleCEntity();
        $objRoleC->setId($someFakeData['account']['roles'][3]['id']);
        $objRoleC->setType($someFakeData['account']['roles'][3]['type']);
        $objRoleC->setC($someFakeData['account']['roles'][3]['c']);
        $objRoleC->setOptions($options);

        $newRoles = $obj->getRoles();
        $newRoles[] = $objRoleC;
        $obj->setRoles($newRoles);

        $form->bind($obj);
        $form->isValid();

        $extractedData = $form->getData(\Zend\Form\FormInterface::VALUES_AS_ARRAY);
        $this->assertEquals($someFakeData, $extractedData);
    }
}
