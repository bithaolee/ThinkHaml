<?php

namespace ThinkHaml\Target;

use ThinkHaml\NodeVisitor\PhpRenderer;
use ThinkHaml\Environment;

class Php extends TargetAbstract
{
    public function __construct(array $options = array())
    {
        parent::__construct($options + array(
            'midblock_regex' => '~else\b|else\s*if\b|catch\b~A',
        ));
    }

    public function getDefaultRendererFactory()
    {
        return function(Environment $env, array $options) {
            return new PhpRenderer($env);
        };
    }
}

