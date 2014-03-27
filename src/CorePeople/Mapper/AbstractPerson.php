<?php

namespace CorePeople\Mapper;

use CoreApplication\Mapper\AbstractMapper;
use CoreApplication\Mapper\ObjectTypeFeatureTrait;

abstract class AbstractPerson extends AbstractMapper
{
    use ObjectTypeFeatureTrait {
        __construct as traitConstructor;
    }

    public $table = 'person';

    public function __construct($_objectTypeId = null)
    {
        $this->traitConstructor($_objectTypeId);
    }
}
