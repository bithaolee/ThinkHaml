<?php

namespace ThinkHaml\Filter;

use ThinkHaml\Node\Filter;
use ThinkHaml\NodeVisitor\RendererAbstract;

interface FilterInterface
{
    public function isOptimizable(RendererAbstract $renderer, Filter $node, $options);

    public function optimize(RendererAbstract $renderer, Filter $node, $options);

    public function filter($content, array $context, $options);
}
