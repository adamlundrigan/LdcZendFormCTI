<?php
namespace LdcZendFormCTI\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Element\Collection;
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\FormCollection;

class FormNonuniformCollection extends AbstractHelper
{
    protected $outerContainerTemplate = 'ldc-zend-form-cti-outer-container-template';

    protected $instanceContainerTemplate = 'ldc-zend-form-cti-instance-container-template';

    protected $instanceTemplateMap = array();

    protected $instanceDiscriminatorFieldName;

    protected $collection;

    public function setOuterContainerTemplate($tpl)
    {
        $this->outerContainerTemplate = $tpl;

        return $this;
    }

    public function setInstanceContainerTemplate($tpl)
    {
        $this->instanceContainerTemplate = $tpl;

        return $this;
    }

    public function setInstanceTemplateMap(array $map)
    {
        $this->instanceTemplateMap = $map;

        return $this;
    }

    public function getInstanceDiscriminatorFieldName()
    {
        return $this->instanceDiscriminatorFieldName;
    }

    public function setInstanceDiscriminatorFieldName($name)
    {
        $this->instanceDiscriminatorFieldName = $name;

        return $this;
    }

    public function setCollection(Collection $c)
    {
        $this->collection = $c;

        return $this;
    }

    public function render()
    {
        return $this->getView()->render($this->outerContainerTemplate, array(
            'helper'     => $this,
            'collection' => $this->collection,
        ));
    }

    public function renderInstances()
    {
        $result = "";

        foreach ($this->collection as $instance) {
            $discr = $instance->get($this->instanceDiscriminatorFieldName)->getValue();
            $result .= $this->getView()->render($this->instanceContainerTemplate, array(
                'helper'       => $this,
                'instance'     => $instance,
                'instanceType' => $discr,
            ));
        }

        return $result;
    }

    public function renderInstance(FieldsetInterface $instance)
    {
        $discr = $instance->get($this->instanceDiscriminatorFieldName)->getValue();

        // If no override template is provided fall back to standard FormCollection render
        if (! isset($this->instanceTemplateMap[$discr])) {
            $row = new FormCollection();
            $row->setView($this->getView());

            return $row($instance);
        }

        return $this->getView()->render($this->instanceTemplateMap[$discr], array(
            'helper'        => $this,
            'instance'      => $instance,
            'instanceType'  => $discr,
        ));
    }

    public function renderTemplateFor(FieldsetInterface $instance)
    {
        $discr = $instance->get($this->instanceDiscriminatorFieldName)->getValue();

        return $this->getView()->render($this->instanceContainerTemplate, array(
            'helper'        => $this,
            'instance'      => $instance,
            'instanceType'  => $discr,
        ));
    }
}
