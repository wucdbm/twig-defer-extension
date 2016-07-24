<?php

namespace Wucdbm\Extension\Twig\Node;

class DeferredNode extends \Twig_Node {

    public function __construct($name, \Twig_Node_Expression $value = null, $line, $tag = null) {
        parent::__construct(array('default' => $value), array('name' => $name), $line, $tag);
    }

    public function compile(\Twig_Compiler $compiler) {
        if ($this->getNode('default')) {
            $compiler
                ->addDebugInfo($this)
                ->write('echo $this->env->getExtension(\'defer\')->has(')
                ->string($this->getAttribute('name'))
                ->raw(") ? ")
                ->write('$this->env->getExtension(\'defer\')->flush(')
                ->string($this->getAttribute('name'))
                ->raw(") : ")
                ->subcompile($this->getNode('default'))
                ->raw(";\n");
        } else {
            $compiler
                ->addDebugInfo($this)
                ->write('echo $this->env->getExtension(\'defer\')->flush(')
                ->string($this->getAttribute('name'))
                ->raw(");\n");
        }
    }

}