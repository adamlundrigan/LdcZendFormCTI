<?php

namespace LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleC;

class OptionTwoEntity
{
    protected $id;
    protected $type;
    protected $secondOption;

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

    public function getSecondOption()
    {
        return $this->secondOption;
    }

    public function setSecondOption($value)
    {
        $this->secondOption = $value;

        return $this;
    }
}
