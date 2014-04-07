<?php

namespace ThinkHaml\Filter;

use ThinkHaml\NodeVisitor\RendererAbstract as Renderer;
use ThinkHaml\Node\Filter;

class Cdata extends Plain
{
    public function optimize(Renderer $renderer, Filter $node, $options)
    {
        $renderer->write('<![CDATA[')->indent();
        $this->renderFilter($renderer, $node);
        $renderer->undent()->write(']]>');
    }
}
