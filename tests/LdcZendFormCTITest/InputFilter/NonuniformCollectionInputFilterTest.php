<?php
namespace LdcZendFormCTITest\InputFilter;

use LdcZendFormCTITest\TestCase;
use LdcZendFormCTI\InputFilter\NonuniformCollectionInputFilter;

class NonuniformCollectionInputFilterTest extends TestCase
{

    public function testSetInputFilterRejectsNonTraversableArgument()
    {
        $this->setExpectedException('Zend\InputFilter\Exception\RuntimeException');

        $obj = new NonuniformCollectionInputFilter();
        $obj->setInputFilter('test');
    }

    public function testSetInputFilterDefersToFormFactoryToCreateElementFromNonElementArgument()
    {
        $targetElement = array('name' => 'foobar');

        $mockElement = \Mockery::mock('Zend\InputFilter\BaseInputFilter');

        $mockFormManager = \Mockery::mock('Zend\InputFilter\Factory');
        $mockFormManager->shouldReceive('createInputFilter')->withArgs(array($targetElement))->once()->andReturn($mockElement);

        $obj = \Mockery::mock('LdcZendFormCTI\InputFilter\NonuniformCollectionInputFilter[getFactory]');
        $obj->shouldReceive('getFactory')->once()->andReturn($mockFormManager);

        $obj->setInputFilter(array('test' => $targetElement));
        $this->assertEquals(array('test' => $mockElement), $obj->getInputFilter());
    }

    public function testSetInputFilterWillThrowExceptionWhenFormFactoryCannotCreateElement()
    {
        $targetElement = array('name' => 'foobar');

        $mockFormManager = \Mockery::mock('Zend\InputFilter\Factory');
        $mockFormManager->shouldReceive('createInputFilter')->withArgs(array($targetElement))->once()->andReturnNull();

        $obj = \Mockery::mock('LdcZendFormCTI\InputFilter\NonuniformCollectionInputFilter[getFactory]');
        $obj->shouldReceive('getFactory')->once()->andReturn($mockFormManager);

        $this->setExpectedException('Zend\InputFilter\Exception\RuntimeException');
        $obj->setInputFilter(array('test' => $targetElement));
    }

    public function testSetInputFilterWillThrowExceptionWhenSuppliedElementIsInvalid()
    {
        $this->setExpectedException('Zend\InputFilter\Exception\RuntimeException');

        $obj = new NonuniformCollectionInputFilter();
        $obj->setInputFilter(array('test' => 'notgonnawork'));
    }

    public function testHappyCaseWorksProperly()
    {
        $dataset = $this->getTestingDataset();

        $inputFilter = $this->getTestingInputFilter();
        $inputFilter->setData($dataset['account']['roles']);
        $this->assertTrue($inputFilter->isValid());
    }

    public function testOmittingRequiredValueTriggersAppropriateErrorMessage()
    {
        $dataset = $this->getTestingDataset();
        unset($dataset['account']['roles'][1]['a']);

        $inputFilter = $this->getTestingInputFilter();
        $inputFilter->setData($dataset['account']['roles']);
        $this->assertFalse($inputFilter->isValid());

        $messages = $inputFilter->getMessages();
        $this->assertArrayHasKey('1', $messages);
        $this->assertArrayHasKey('a', $messages[1]);
        $this->assertArrayHasKey('isEmpty', $messages[1]['a']);
    }

    public function testEnsureThatInputFilterInstanceIsNotReusedWhenMultipleElementsOfTheSameTypeAreValidated()
    {
        $dataset = $this->getTestingDataset();
        unset($dataset['account']['roles'][0]['b']);

        $inputFilter = $this->getTestingInputFilter();
        $inputFilter->setData($dataset['account']['roles']);
        $this->assertFalse($inputFilter->isValid());

        $messages = $inputFilter->getMessages();
        $this->assertArrayHasKey('0', $messages);
        $this->assertArrayHasKey('b', $messages[0]);
        $this->assertArrayHasKey('isEmpty', $messages[0]['b']);

        // If the InputFilter instance were reused, the second time ('2') would
        // also fail because the data used in the first validation would carry through
        // @see https://github.com/zendframework/zf2/issues/6304
        $this->assertArrayNotHasKey('2', $messages);
    }

    public function testValidatesEmptyDataWhenIsRequiredIsFalse()
    {
        $inputFilter = $this->getTestingInputFilter();
        $inputFilter->setIsRequired(false);
        $inputFilter->setData(array());
        $this->assertTrue($inputFilter->isValid());
    }

    public function testRejectsEmptyDataWhenIsRequiredIsTrue()
    {
        $inputFilter = $this->getTestingInputFilter();
        $inputFilter->setIsRequired(true);
        $inputFilter->setData(array());
        $this->assertFalse($inputFilter->isValid());
    }

    public function testIsValidRejectsWhenSuppliedDataHasTooFewElements()
    {
        $dataset = $this->getTestingDataset();

        $inputFilter = $this->getTestingInputFilter();
        $inputFilter->setData($dataset['account']['roles']);
        $inputFilter->setCount(99);
        $this->assertFalse($inputFilter->isValid());
    }

    public function testPassingDataWithoutKnownValidator()
    {
        $obj = new NonuniformCollectionInputFilter();
        $obj->setDiscriminatorFieldName('test');
        $obj->setData(array(array('test' => 'notfound')));

        $this->assertFalse($obj->isValid());

        $messages = $obj->getMessages();
        $this->assertArrayHasKey('0', $messages);
        $this->assertArrayHasKey('test', $messages[0]);
        $this->assertStringStartsWith('Could not map provided value (notfound)', $messages[0]['test']);
    }
}
