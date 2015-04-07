<?php

namespace LdcZendFormCTI\Form\Element;

use Traversable;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Fieldset;
use Zend\Form\FieldsetInterface;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Form\Element\Collection;

class NonuniformCollection extends Collection
{
    protected $discriminatorFieldName = '___class';

    protected $targetElement = array();

    protected $templateElement = null;

    protected $shouldCreateChildrenOnPrepareElement = false;

    /**
     * Additional accepted options added for NonuniformCollection:
     * - discriminator_field_name: the data key which holds the discriminator.
     *
     * @param array|Traversable $options
     *
     * @return Collection
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['discriminator_field_name'])) {
            $this->setDiscriminatorFieldName($options['discriminator_field_name']);
        }

        return $this;
    }

    /**
     * Set name of form field which will be used as discriminator.
     *
     * @param string $fn
     *
     * @return Collection
     */
    public function setDiscriminatorFieldName($fn)
    {
        $this->discriminatorFieldName = $fn;

        return $this;
    }

    /**
     * Retrieve name of form field used as discriminator.
     *
     * @return string
     */
    public function getDiscriminatorFieldName()
    {
        return $this->discriminatorFieldName;
    }

    /**
     * Set the target element.
     *
     * @param array|Traversable $set
     *
     * @return Collection
     *
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setTargetElement($set)
    {
        if (! is_array($set) && ! $set instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that $set be an array or object implementing Traversable; received "%s"',
                __METHOD__,
                (is_object($set) ? get_class($set) : gettype($set))
            ));
        }

        foreach ($set as $discriminator => $elementOrFieldset) {
            if (is_array($elementOrFieldset)
               || ($elementOrFieldset instanceof Traversable && !$elementOrFieldset instanceof ElementInterface)
            ) {
                $factory = $this->getFormFactory();
                $elementOrFieldset = $factory->create($elementOrFieldset);
            }

            if (!$elementOrFieldset instanceof ElementInterface) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '%s requires that $elementOrFieldset be an object implementing %s; received "%s"',
                    __METHOD__,
                    __NAMESPACE__.'\ElementInterface',
                    (is_object($elementOrFieldset) ? get_class($elementOrFieldset) : gettype($elementOrFieldset))
                ));
            }
            $this->targetElement[$discriminator] = $elementOrFieldset;
        }

        return $this;
    }

    /**
     * Prepare the collection by adding a dummy template element if the user want one.
     *
     * @param FormInterface $form
     *
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();

        // Purposefully omitted the shouldCreateChildrenOnPrepareElement block
        // as that functionality is not applicable in this case
        // (only works when there is a single target element)

        // Create a template that will also be prepared
        if ($this->shouldCreateTemplate) {
            $templateElement = $this->getTemplateElement();
            foreach ((array) $templateElement as $elementOrFieldset) {
                $elementOrFieldset->setName($name.'['.$elementOrFieldset->getName().']');

                // Recursively prepare elements
                if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                    $elementOrFieldset->prepareElement($form);
                }
            }
        }

        // Zend\Form\Fieldset::prepareElement
        foreach ($this->iterator as $elementOrFieldset) {
            $elementOrFieldset->setName($this->getName().'['.$elementOrFieldset->getName().']');

            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }

    /**
     * Populate values.
     *
     * @param array|Traversable $data
     *
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     */
    public function populateValues($data)
    {
        if (!is_array($data) && !$data instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of data; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        // Can't do anything with empty data
        if (empty($data)) {
            return;
        }

        if (!$this->allowRemove && count($data) < $this->count) {
            throw new Exception\DomainException(sprintf(
                'There are fewer elements than specified in the collection (%s). Either set the allow_remove option '
                .'to true, or re-submit the form.',
                get_class($this)
            ));
        }

        // Check to see if elements have been replaced or removed
        foreach ($this->iterator as $name => $elementOrFieldset) {
            if (isset($data[$name])) {
                continue;
            }

            if (!$this->allowRemove) {
                throw new Exception\DomainException(sprintf(
                    'Elements have been removed from the collection (%s) but the allow_remove option is not true.',
                    get_class($this)
                ));
            }

            $this->remove($name);
        }

        $discrKey = $this->getDiscriminatorFieldName();

        foreach ($data as $key => $value) {
            if ($this->has($key)) {
                $elementOrFieldset = $this->get($key);
            } else {
                $elementOrFieldset = $this->addNewTargetElementInstance($key, $value[$discrKey]);

                if ($key > $this->lastChildIndex) {
                    $this->lastChildIndex = $key;
                }
            }

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $elementOrFieldset->populateValues($value);
            } else {
                $elementOrFieldset->setAttribute('value', $value);
            }
        }

        if (!$this->createNewObjects()) {
            $this->replaceTemplateObjects();
        }
    }

    public function extract()
    {
        if ($this->object instanceof Traversable) {
            $this->object = ArrayUtils::iteratorToArray($this->object);
        }

        if (!is_array($this->object)) {
            return array();
        }

        $discrKey = $this->getDiscriminatorFieldName();
        $methodFilter = new \Zend\Filter\Word\UnderscoreToCamelCase();
        $discrMethod = 'get'.$methodFilter->filter($discrKey);

        $values = array();
        foreach ($this->object as $key => $value) {
            $discriminator = $value->{$discrMethod}();

            if ($this->targetElement[$discriminator] instanceof FieldsetInterface && $value instanceof $this->targetElement[$discriminator]->object) {
                // @see https://github.com/zendframework/zf2/pull/2848
                $targetElement = clone $this->targetElement[$discriminator];
                $targetElement->object = $value;
                $values[$key] = $targetElement->extract();

                if (!isset($values[$key][$discrKey])) {
                    $values[$key][$discrKey] = $discriminator;
                }

                if ($this->has($key)) {
                    $fieldset = $this->get($key);
                    if ($fieldset instanceof Fieldset && $fieldset->allowObjectBinding($value)) {
                        $fieldset->setObject($value);
                    }
                }
            }
        }

        return $values;
    }

    /**
     * Create a new instance of the target element.
     *
     * @param string $discriminator Discriminator of target element
     *
     * @return ElementInterface
     */
    protected function createNewTargetElementInstance($discriminator = null)
    {
        if (!isset($this->targetElement[$discriminator])) {
            return;
        }

        return clone $this->targetElement[$discriminator];
    }

    /**
     * Add a new instance of the target element.
     *
     * @param string $name
     * @param string $discriminator
     *
     * @return ElementInterface
     *
     * @throws Exception\DomainException
     */
    protected function addNewTargetElementInstance($name, $discriminator = null)
    {
        $this->shouldCreateChildrenOnPrepareElement = false;

        $elementOrFieldset = $this->createNewTargetElementInstance($discriminator);
        if (! $elementOrFieldset) {
            throw new Exception\DomainException(sprintf(
                'The discriminator you supplied (%s) is not valid',
                $discriminator
            ));
        }
        $elementOrFieldset->setName($name);

        $this->add($elementOrFieldset);

        if (!$this->allowAdd && $this->count() > $this->count) {
            throw new Exception\DomainException(sprintf(
                'There are more elements than specified in the collection (%s). Either set the allow_add option '.
                'to true, or re-submit the form.',
                get_class($this)
            ));
        }

        return $elementOrFieldset;
    }

    /**
     * Create a dummy template element.
     *
     * @return null|ElementInterface|FieldsetInterface
     */
    protected function createTemplateElement()
    {
        if (!$this->shouldCreateTemplate) {
            return;
        }

        if ($this->templateElement) {
            return $this->templateElement;
        }

        $element = array();
        foreach ($this->targetElement as $discr => $fieldset) {
            $element[$discr] = clone $fieldset;
            $element[$discr]->get($this->getDiscriminatorFieldName())->setValue($discr);
            $element[$discr]->setName($this->templatePlaceholder);
        }

        return $element;
    }
}
