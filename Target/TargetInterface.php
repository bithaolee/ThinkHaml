<?php

namespace ThinkHaml\Target;

use ThinkHaml\Environment;
use ThinkHaml\Node\NodeAbstract;

interface TargetInterface
{
    function parse(Environment $env, $string, $filename);
    function compile(Environment $env, NodeAbstract $node);
}

