<?php
namespace LdcZendFormCTI\Form\Element;

use Zend\Stdlib\Hydrator\HydratorInterface;

class NonuniformCollectionHydrator implements HydratorInterface
{
    /**
     * Decorated Hydrator (the one that actually does the work)
     *
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * Decorate an existing hydrator instance to make it CTI-friendly
     *
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Extract values from an object
     *
     * We inject an ___class key into the extracted array structure to
     * denote which member of the CTI the serialized structure represents
     *
     * @param  object $object
     * @return array
     */
    public function extract($object)
    {
        $data = $this->hydrator->extract($object);
        $data['___class'] = get_class($object);

        return $data;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * If an ___class key is provided with the data array we use that FQCN
     * to create an object instance for the hydrator to work against
     *
     * @param  array  $data
     * @param  object $object
     * @return object
     */
    public function hydrate(array $data, $object)
    {
        $obj = (isset($data['___class']) && class_exists($data['___class'])) ? new $data['___class'] : $object;
        unset($data['___class']);

        return $this->hydrator->hydrate($data, $obj);
    }

}
