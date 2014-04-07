<?php

namespace ThinkHaml\Node;

use ThinkHaml\Node\TagAttribute;
use ThinkHaml\NodeVisitor\NodeVisitorInterface;

class TagAttributeList extends TagAttribute
{
    public function __construct(array $position, NodeAbstract $value = null)
    {
        parent::__construct($position, null, $value);
    }

    public function accept(NodeVisitorInterface $visitor)
    {
        if (false !== $visitor->enterTagAttribute($this)) {
            if (false !== $visitor->enterTagAttributeList($this)) {
                $this->getValue()->accept($visitor);
            }
            $visitor->leaveTagAttributeList($this);
        }
        $visitor->leaveTagAttribute($this);
    }
}
