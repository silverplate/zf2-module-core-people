<?php

namespace CorePeople\Entity;

use Application\Entity\ObjectType;

class Person extends AbstractPerson
{
    /**
     * @param int $_objectTypeId
     */
    public function __construct($_objectTypeId = null)
    {
        parent::__construct($_objectTypeId ?: ObjectType::PERSON);
    }
}
