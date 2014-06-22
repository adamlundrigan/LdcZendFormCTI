<?php
namespace LdcZendFormCTI\InputFilter;

use Traversable;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Exception;
use Zend\InputFilter\BaseInputFilter;

class NonuniformCollectionInputFilter extends CollectionInputFilter
{
    protected $discriminatorFieldName = '___class';

    protected $inputFilter = array();

    /**
     * Set name of form field which will be used as discriminator
     *
     * @param  string     $fn
     * @return Collection
     */
    public function setDiscriminatorFieldName($fn)
    {
        $this->discriminatorFieldName = $fn;

        return $this;
    }

    /**
     * Retrieve name of form field used as discriminator
     *
     * @return string
     */
    public function getDiscriminatorFieldName()
    {
        return $this->discriminatorFieldName;
    }

    /**
     * Set the input filter to use when looping the data
     *
     * @param  array|Traversable          $inputFilter
     * @throws Exception\RuntimeException
     * @return CollectionInputFilter
     */
    public function setInputFilter($filterSet)
    {
        if ( ! is_array($filterSet) && ! $filterSet instanceof Traversable) {
            throw new Exception\RuntimeException(sprintf(
                '%s expects an array of instances of %s; received "%s"',
                __METHOD__,
                'Zend\InputFilter\BaseInputFilter',
                (is_object($filterSet) ? get_class($filterSet) : gettype($filterSet))
            ));
        }

        $this->inputFilter = array();
        foreach ($filterSet as $discriminator => $inputFilter) {
            if (is_array($inputFilter) || $inputFilter instanceof Traversable) {
                $inputFilter = $this->getFactory()->createInputFilter($inputFilter);
            }

            if (!$inputFilter instanceof BaseInputFilter) {
                throw new Exception\RuntimeException(sprintf(
                    '%s expects an array of instances of %s; received "%s"',
                    __METHOD__,
                    'Zend\InputFilter\BaseInputFilter',
                    (is_object($inputFilter) ? get_class($inputFilter) : gettype($inputFilter))
                ));
            }

            $this->inputFilter[$discriminator] = $inputFilter;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $valid = true;

        if ($this->getCount() < 1) {
            if ($this->isRequired) {
                $valid = false;
            }
        }

        $data = isset($this->collectionData)
              ? $data = $this->collectionData
              : $data = $this->data;

        if (count($data) < $this->getCount()) {
            $valid = false;
        }

        if (empty($data)) {
            if ( method_exists($this, 'clearValues') ) {
                $this->clearValues();
                $this->clearRawValues();
            }

            return $valid;
        }

        $filterSet = $this->getInputFilter();
        $discrKey  = $this->getDiscriminatorFieldName();

        foreach ($data as $key => $data) {
            if (!is_array($data)) {
                $data = array();
            }
            if ( ! isset($filterSet[$data[$discrKey]]) ) {
                $valid = false;
                $this->collectionMessages[$key] = array(
                    $discrKey => sprintf('Could not map provided value (%s) to an input filter', $data[$discrKey])
                );
                $this->invalidInputs[$key] = array(
                    $discrKey => $data[$discrKey]
                );
                continue;
            }

            $inputFilter = clone $filterSet[$data[$discrKey]];
            $inputFilter->setData($data);

            if (null !== $this->validationGroup) {
                $inputFilter->setValidationGroup($this->validationGroup[$key]);
            }

            if ($inputFilter->isValid()) {
                $this->validInputs[$key] = $inputFilter->getValidInput();
            } else {
                $valid = false;
                $this->collectionMessages[$key] = $inputFilter->getMessages();
                $this->invalidInputs[$key] = $inputFilter->getInvalidInput();
            }

            $this->collectionValues[$key] = $inputFilter->getValues();
            $this->collectionRawValues[$key] = $inputFilter->getRawValues();
        }

        return $valid;
    }

}
