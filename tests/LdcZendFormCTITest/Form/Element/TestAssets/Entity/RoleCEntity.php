<?php
namespace LdcZendFormCTITest\Form\Element\TestAssets\Entity;

class RoleCEntity
{
    protected $id;
    protected $type;
    protected $c;
    protected $options = array();

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getC()
    {
        return $this->c;
    }

    public function setC($value)
    {
        $this->c = $value;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
