<?php

namespace ThinkHaml\Runtime;

class AttributeList
{
    public $attributes;

    static public function create($attributes)
    {
        $instance = new AttributeList;
        $instance->attributes = $attributes;
        return $instance;
    }
}
