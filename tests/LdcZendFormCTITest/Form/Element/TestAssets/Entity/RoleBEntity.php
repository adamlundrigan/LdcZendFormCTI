<?php
namespace LdcZendFormCTITest\Form\Element\TestAssets\Entity;

class RoleBEntity
{
    protected $id;
    protected $type;
    protected $b;

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

    public function getB()
    {
        return $this->b;
    }

    public function setB($value)
    {
        $this->b = $value;

        return $this;
    }
}
