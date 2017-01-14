<?php

namespace Wucdbm\Extension\Twig\Node;

use Wucdbm\Extension\Twig\Extension\DeferExtension;

class DeferredNode extends \Twig_Node {

    public function __construct($name, \Twig_Node_Expression $value = null, $line, $tag = null) {
        $nodes = [];

        if ($value) {
            $nodes['name'] = $value;
        }

        parent::__construct($nodes, array('name' => $name), $line, $tag);
    }

    public function compile(\Twig_Compiler $compiler) {
        if ($this->hasNode('name')) {
            $compiler
                ->addDebugInfo($this)
                ->write(sprintf('echo $this->env->getExtension(\'%s\')->has(', DeferExtension::class))
                ->string($this->getAttribute('name'))
                ->raw(") ? ")
                ->write(sprintf('$this->env->getExtension(\'%s\')->flush(', DeferExtension::class))
                ->string($this->getAttribute('name'))
                ->raw(") : ")
                ->subcompile($this->getNode('name'))
                ->raw(";\n");
        } else {
            $compiler
                ->addDebugInfo($this)
                ->write(sprintf('echo $this->env->getExtension(\'%s\')->flush(', DeferExtension::class))
                ->string($this->getAttribute('name'))
                ->raw(");\n");
        }
    }

}