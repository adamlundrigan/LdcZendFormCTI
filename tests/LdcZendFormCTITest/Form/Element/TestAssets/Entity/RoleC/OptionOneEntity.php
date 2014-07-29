<?php
namespace LdcZendFormCTITest\Form\Element\TestAssets\Entity\RoleC;

class OptionOneEntity
{
    protected $id;
    protected $type;
    protected $firstOption;

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

    public function getFirstOption()
    {
        return $this->firstOption;
    }

    public function setFirstOption($value)
    {
        $this->firstOption = $value;

        return $this;
    }
}
