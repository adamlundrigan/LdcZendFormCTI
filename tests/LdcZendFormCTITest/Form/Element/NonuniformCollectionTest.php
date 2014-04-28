<?php
namespace LdcZendFormCTITest\Form\Element;

use LdcZendFormCTITest\TestCase;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Form\Form;
use LdcZendFormCTI\Form\Element\NonuniformCollection;
use LdcZendFormCTI\Form\Element\NonuniformCollectionHydrator;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\AccountEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\AccountFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleAEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleAFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleBFieldset;

class NonuniformCollectionTest extends TestCase
{
    public function testBasicSmokeTestForEntityHydrationFromAlreadyPreprocessedFormData()
    {

        $form = new Form();

        // Add the 'account' fieldset as the base fieldset
        $fsAccount = new AccountFieldset();
        $fsAccount->setUseAsBaseFieldset(true);
        $fsAccount->setHydrator(new ClassMethods(false));
        $fsAccount->setObject(new AccountEntity());

        // Create a fieldset for each role type, and seed them with the prototype object
        // and an instance of NonuniformCollectionHydrator

        $fsRoleA = new RoleAFieldset();
        $fsRoleA->setObject(new RoleAEntity());
        $fsRoleA->setHydrator(new NonuniformCollectionHydrator(new ClassMethods(false)));

        $fsRoleB = new RoleBFieldset();
        $fsRoleB->setObject(new RoleBEntity());
        $fsRoleB->setHydrator(new NonuniformCollectionHydrator(new ClassMethods(false)));

        // Build the NonuniformCollection by telling it what entitiy types to expect and
        // what fieldset is associated with each one.  Attach it to the Account fieldset
        $collAccountRoles = new NonuniformCollection();
        $collAccountRoles->setName('roles');
        $collAccountRoles->setTargetElement(array(
            'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleAEntity' => $fsRoleA,
            'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity' => $fsRoleB,
        ));
        $fsAccount->add($collAccountRoles);

        $form->add($fsAccount);

        // This is what the POST data from our form should look like after it's been preprocessed
        // to convert the value of the 'type' field into the corresponding FQCN ___class key
        $someFakeData = array(
            'account' => array(
                'id' => 123,
                'name' => 'Foobar Bazbat',
                'roles' => array(
                    array(
                        '___class' => 'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity',
                        'id' => 42,
                        'type' => 'b',
                        'b' => 'foo',
                    ),
                    array(
                        '___class' => 'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleAEntity',
                        'id' => 18,
                        'type' => 'a',
                        'a' => 'bar',
                    ),
                    array(
                        '___class' => 'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity',
                        'id' => 99,
                        'type' => 'b',
                        'b' => 'baz',
                    ),
                ),
            ),
        );

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
        $form = new Form();

        // Add the 'account' fieldset as the base fieldset
        $fsAccount = new AccountFieldset();
        $fsAccount->setUseAsBaseFieldset(true);
        $fsAccount->setHydrator(new ClassMethods(false));
        $fsAccount->setObject(new AccountEntity());

        // Create a fieldset for each role type, and seed them with the prototype object
        // and an instance of NonuniformCollectionHydrator

        $fsRoleA = new RoleAFieldset();
        $fsRoleA->setObject(new RoleAEntity());
        $fsRoleA->setHydrator(new NonuniformCollectionHydrator(new ClassMethods(false)));

        $fsRoleB = new RoleBFieldset();
        $fsRoleB->setObject(new RoleBEntity());
        $fsRoleB->setHydrator(new NonuniformCollectionHydrator(new ClassMethods(false)));

        // Build the NonuniformCollection by telling it what entitiy types to expect and
        // what fieldset is associated with each one.  Attach it to the Account fieldset
        $collAccountRoles = new NonuniformCollection();
        $collAccountRoles->setName('roles');
        $collAccountRoles->setTargetElement(array(
            'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleAEntity' => $fsRoleA,
            'LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity' => $fsRoleB,
        ));
        $collAccountRoles->setShouldCreateTemplate($shouldPrepareTemplate);
        $fsAccount->add($collAccountRoles);
        $form->add($fsAccount);
        $form->prepare();

        $templates = $collAccountRoles->getTemplateElement();
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
