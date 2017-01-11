<?php

namespace Wucdbm\Extension\Twig\Node;

use Wucdbm\Extension\Twig\Extension\DeferExtension;

class DeferredNode extends \Twig_Node {

    public function __construct($name, \Twig_Node_Expression $value = null, $line, $tag = null) {
        parent::__construct(array('default' => $value), array('name' => $name), $line, $tag);
        ;
    }

    public function compile(\Twig_Compiler $compiler) {
        if ($this->getNode('default')) {
            $compiler
                ->addDebugInfo($this)
                ->write(sprintf('echo $this->env->getExtension(\'%s\')->has(', DeferExtension::class))
                ->string($this->getAttribute('name'))
                ->raw(") ? ")
                ->write(sprintf('$this->env->getExtension(\'%s\')->flush(', DeferExtension::class))
                ->string($this->getAttribute('name'))
                ->raw(") : ")
                ->subcompile($this->getNode('default'))
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