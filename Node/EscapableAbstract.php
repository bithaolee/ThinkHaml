<?php

namespace ThinkHaml\Node;

use ThinkHaml\Escaping;

abstract class EscapableAbstract extends NodeAbstract
{
    private $escaping;

    public function getEscaping()
    {
        if (null === $this->escaping) {
            $this->escaping = new Escaping;
        }
        return $this->escaping;
    }
}

