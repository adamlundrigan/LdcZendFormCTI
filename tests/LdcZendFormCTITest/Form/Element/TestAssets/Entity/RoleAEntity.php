<?php

namespace LdcZendFormCTITest\Form\Element\TestAssets\Entity;

class RoleAEntity
{
    protected $id;
    protected $type;
    protected $a;

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

    public function getA()
    {
        return $this->a;
    }

    public function setA($value)
    {
        $this->a = $value;

        return $this;
    }
}
