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
    protected $targetElement = array();

    /**
     * Set the target element
     *
     * @param  ElementInterface|array|Traversable            $elementOrFieldset
     * @return Collection
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function setTargetElement($set)
    {
        foreach ($set as $elementOrFieldset) {
            $discriminator = get_class($elementOrFieldset->getObject());
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
                    __NAMESPACE__ . '\ElementInterface',
                    (is_object($elementOrFieldset) ? get_class($elementOrFieldset) : gettype($elementOrFieldset))
                ));
            }
            $this->targetElement[$discriminator] = $elementOrFieldset;
        }

        return $this;
    }
    /**
     * If both count and targetElement are set, add them to the fieldset
     *
     * @return void
     */
    public function prepareFieldset()
    {
        // @TODO We don't do any advance fieldset construction. Should we?
    }


    /**
     * Prepare the collection by adding a dummy template element if the user want one
     *
     * @param  FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();

        // Create a template that will also be prepared
        if ($this->shouldCreateTemplate) {
            $templateElement = $this->getTemplateElement();
            foreach ( (array) $templateElement as $elementOrFieldset ) {
                $elementOrFieldset->setName($name . '[' . $elementOrFieldset->getName() . ']');

                // Recursively prepare elements
                if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                    $elementOrFieldset->prepareElement($form);
                }
            }
        }

        // Zend\Form\Fieldset::prepareElement
        foreach ($this->byName as $elementOrFieldset) {
            $elementOrFieldset->setName($name . '[' . $elementOrFieldset->getName() . ']');

            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }

        // The template element has been prepared, but we don't want it to be rendered nor validated, so remove it from the list
        if ($this->shouldCreateTemplate) {
            foreach ( (array) $this->templatePlaceholder as $item ) {
                $this->remove($item);
            }
        }
    }

    /**
     * Populate values
     *
     * @param  array|Traversable                             $data
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     * @return void
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

        if (count($data) < $this->getCount()) {
            if (!$this->allowRemove) {
                throw new Exception\DomainException(sprintf(
                    'There are fewer elements than specified in the collection (%s). Either set the allow_remove option ' .
                    'to true, or re-submit the form.',
                    get_class($this)
                    )
                );
            }

            // If there are less data and that allowRemove is true, we remove elements that are not presents
            $this->setCount(count($data));
            foreach ($this->byName as $name => $elementOrFieldset) {
                if (isset($data[$name])) {
                    continue;
                }

                $this->remove($name);
            }
        }

        if ( ! empty($this->targetElement) ) {
            foreach ($this->byName as $name => $fieldset) {
                if (isset($data[$name])) {
                    $fieldset->populateValues($data[$name]);
                    unset($data[$name]);
                }
            }
        } else {
            foreach ($this->byName as $name => $element) {
                $element->setAttribute('value', $data[$name]);
                unset($data[$name]);
            }
        }

        // If there are still data, this means that elements or fieldsets were dynamically added. If allowed by the user, add them
        if (!empty($data) && $this->allowAdd) {
            foreach ($data as $key => $value) {
                $elementOrFieldset = clone $this->targetElement[$value['___class']];
                $elementOrFieldset->setName($key);

                if ($elementOrFieldset instanceof FieldsetInterface) {
                    $elementOrFieldset->populateValues($value);
                } else {
                    $elementOrFieldset->setAttribute('value', $value);
                }

                $this->add($elementOrFieldset);
            }
        } elseif (!empty($data) && !$this->allowAdd) {
            throw new Exception\DomainException(sprintf(
                'There are more elements than specified in the collection (%s). Either set the allow_add option ' .
                'to true, or re-submit the form.',
                get_class($this)
                )
            );
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

        $values = array();
        foreach ($this->object as $key => $value) {
            $discriminator = get_class($value);
            $hydrator = $this->targetElement[$discriminator]->getHydrator();
            if (is_callable(array($hydrator, 'extract'))) {
                $values[$key] = $hydrator->extract($value);
            } elseif ($value instanceof $this->targetElement[$discriminator]->object) {
                // @see https://github.com/zendframework/zf2/pull/2848
                $targetElement = clone $this->targetElement[$discriminator];
                $targetElement->object = $value;
                $values[$key] = $targetElement->extract();
                if ($this->has($key)) {
                    $fieldset = $this->get($key);
                    if ($fieldset instanceof Fieldset && $fieldset->allowObjectBinding($value)) {
                        $fieldset->setObject($value);
                    }
                }
            }
            unset($values['___class']);
        }

        return $values;
    }

    protected $templateElement = array();
    public function getTemplateElement()
    {
        if (empty($this->templateElement)) {
            $this->templateElement = array();
            foreach ($this->targetElement as $class=>$fieldset) {
                $this->templateElement[$class] = clone $fieldset;
                $this->templateElement[$class]->setName($this->templatePlaceholder);
            }
        }

        return $this->templateElement;
    }
}
