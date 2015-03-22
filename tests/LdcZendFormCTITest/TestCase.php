<?php
namespace LdcZendFormCTITest;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Form\Form;
use LdcZendFormCTI\InputFilter\NonuniformCollectionInputFilter;
use LdcZendFormCTI\Form\Element\NonuniformCollection;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\AccountEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\AccountFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleAEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleAFieldset;
use LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleBEntity;
use LdcZendFormCTITest\Form\Element\TestAssets\Fieldset\RoleBFieldset;

/**
 * Base test case to be used when a service manager instance is required.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected static $configuration = array();

    /**
     * @static
     *
     * @param array $configuration
     */
    public static function setConfiguration(array $configuration)
    {
        static::$configuration = $configuration;
    }

    /**
     * @static
     *
     * @return array
     */
    public static function getConfiguration()
    {
        return static::$configuration;
    }

    /**
     * Retrieves a new ServiceManager instance.
     *
     * @param array|null $configuration
     *
     * @return ServiceManager
     */
    public function getServiceManager(array $configuration = null)
    {
        $configuration = $configuration ?: static::getConfiguration();
        $serviceManager = new ServiceManager(
            new ServiceManagerConfig(
                isset($configuration['service_manager']) ? $configuration['service_manager'] : array()
            )
        );

        $serviceManager->setService('ApplicationConfig', $configuration);
        $serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');

        /* @var $moduleManager \Zend\ModuleManager\ModuleManagerInterface */
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        return $serviceManager;
    }

    public function getTestingDataset()
    {
        return array(
            'account' => array(
                'id' => 123,
                'name' => 'Foobar Bazbat',
                'roles' => array(
                    array(
                        'id' => 42,
                        'type' => 'b',
                        'b' => 'foo',
                    ),
                    array(
                        'id' => 18,
                        'type' => 'a',
                        'a' => 'bar',
                    ),
                    array(
                        'id' => 99,
                        'type' => 'b',
                        'b' => 'baz',
                    ),
                ),
            ),
        );
    }

    public function getTestingEntity()
    {
        $someFakeData = $this->getTestingDataset();

        // Build the test entity structure
        $roles[0] = new RoleBEntity();
        $roles[0]->setId($someFakeData['account']['roles'][0]['id']);
        $roles[0]->setType($someFakeData['account']['roles'][0]['type']);
        $roles[0]->setB($someFakeData['account']['roles'][0]['b']);

        $roles[1] = new RoleAEntity();
        $roles[1]->setId($someFakeData['account']['roles'][1]['id']);
        $roles[1]->setType($someFakeData['account']['roles'][1]['type']);
        $roles[1]->setA($someFakeData['account']['roles'][1]['a']);

        $roles[2] = new RoleBEntity();
        $roles[2]->setId($someFakeData['account']['roles'][2]['id']);
        $roles[2]->setType($someFakeData['account']['roles'][2]['type']);
        $roles[2]->setB($someFakeData['account']['roles'][2]['b']);

        $obj = new AccountEntity();
        $obj->setId($someFakeData['account']['id']);
        $obj->setName($someFakeData['account']['name']);
        $obj->setRoles($roles);

        return $obj;
    }

    public function getTestingForm()
    {
        $form = new Form();

        // Add the 'account' fieldset as the base fieldset
        $fsAccount = new AccountFieldset();
        $fsAccount->setUseAsBaseFieldset(true);
        $fsAccount->setHydrator(new ClassMethods(false));
        $fsAccount->setObject(new AccountEntity());

        // Create a fieldset for each role type, and seed them with the prototype object and hydrator

        $fsRoleA = new RoleAFieldset();
        $fsRoleA->setObject(new RoleAEntity());
        $fsRoleA->setHydrator(new ClassMethods(false));

        $fsRoleB = new RoleBFieldset();
        $fsRoleB->setObject(new RoleBEntity());
        $fsRoleB->setHydrator(new ClassMethods(false));

        // Build the NonuniformCollection by telling it what entitiy types to expect and
        // what fieldset is associated with each one.  Attach it to the Account fieldset
        $collAccountRoles = new NonuniformCollection();
        $collAccountRoles->setName('roles');
        $collAccountRoles->setDiscriminatorFieldName('type');
        $collAccountRoles->setTargetElement(array(
            'a' => $fsRoleA,
            'b' => $fsRoleB,
        ));
        $fsAccount->add($collAccountRoles);

        $form->add($fsAccount);

        return $form;
    }

    public function getTestingInputFilter()
    {
        $inputFilter = new NonuniformCollectionInputFilter();
        $inputFilter->setDiscriminatorFieldName('type');
        $inputFilter->setInputFilter(array(
            'a' => new InputFilter\TestAssets\RoleAInputFilter(),
            'b' => new InputFilter\TestAssets\RoleBInputFilter(),
        ));

        return $inputFilter;
    }
}
