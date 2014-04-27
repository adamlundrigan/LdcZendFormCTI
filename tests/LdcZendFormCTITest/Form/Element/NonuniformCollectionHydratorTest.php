<?php
namespace LdcZendFormCTITest\Form\Element;

use LdcZendFormCTITest\TestCase;
use LdcZendFormCTI\Form\Element\NonuniformCollectionHydrator;
use Zend\Stdlib\Hydrator\ClassMethods;

class NonuniformCollectionHydratorTest extends TestCase
{
    protected $entityClassName;
    protected $decoratedHydrator;

    public function setUp()
    {
        $this->entityClassName = 'LdcZendFormCTITest\Form\Element\TestAssets\SimpleEntity';
        $this->decoratedHydrator = new ClassMethods(false);
    }

    public function testHydrateWillPopulateEntityOfSpecifiedClass()
    {
        $data = array(
            '___class' => $this->entityClassName,
            'id' => '123',
            'name' => 'Foobar Bazbat',
        );

        $obj = new NonuniformCollectionHydrator($this->decoratedHydrator);
        $entity = $obj->hydrate($data, NULL);
        $this->assertEquals($this->entityClassName, get_class($entity));
    }

    public function testHydrateUseTheProvidedEntityIfNoClassIsSpecifiedInData()
    {
        $data = array(
            'id' => '123',
            'name' => 'Foobar Bazbat'
        );

        $obj = new NonuniformCollectionHydrator($this->decoratedHydrator);
        $entity = $obj->hydrate($data, new $this->entityClassName());
        $this->assertEquals($this->entityClassName, get_class($entity));
    }

    public function testExtract()
    {
        $entity = new $this->entityClassName();

        $obj = new NonuniformCollectionHydrator($this->decoratedHydrator);
        $data = $obj->extract($entity);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('___class', $data);
        $this->assertEquals(get_class($entity), $data['___class']);
    }

    public function testRoundTrip()
    {
        $data = array(
            '___class' => $this->entityClassName,
            'id' => '123',
            'name' => 'Foobar Bazbat',
        );

        $obj = new NonuniformCollectionHydrator($this->decoratedHydrator);
        $entity = $obj->hydrate($data, NULL);
        $this->assertEquals($this->entityClassName, get_class($entity));

        $extractedData = $obj->extract($entity);
        $this->assertEquals($data, $extractedData);
    }
}
