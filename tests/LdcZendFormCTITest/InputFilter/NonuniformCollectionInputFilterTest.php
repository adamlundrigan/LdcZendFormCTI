<?php
namespace LdcZendFormCTITest\InputFilter;

use LdcZendFormCTITest\TestCase;

class NonuniformCollectionInputFilterTest extends TestCase
{

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

}
