<?php

namespace Wucdbm\Extension\Twig\Node;

use Wucdbm\Extension\Twig\Extension\DeferExtension;

class DeferNode extends \Twig_Node {

    /** @var \Twig_Node */
    protected $keyNode;

    /** @var \Twig_Node */
    protected $value;


    public function __construct(\Twig_Node $keyNode, \Twig_Node $value, $line, $tag = null) {
        $this->keyNode = $keyNode;
        $this->value = $value;
        parent::__construct([], [], $line, $tag);
    }

    public function compile(\Twig_Compiler $compiler) {
        if ($this->value instanceof \Twig_Node_Expression) {
            $compiler
                ->addDebugInfo($this)
                ->write(sprintf('$this->env->getExtension(\'%s\')->defer(', DeferExtension::class))
                ->subcompile($this->keyNode)
                ->raw(', ')
                ->subcompile($this->value)
                ->raw(");\n");
        } else {
            $compiler
                ->addDebugInfo($this)
                ->write('ob_start();')
                    ->indent()
                        ->subcompile($this->value)
                    ->outdent()
                ->write('; $_deferred = ob_get_clean();')
                ->write(sprintf('$this->env->getExtension(\'%s\')->defer(', DeferExtension::class))
                    ->subcompile($this->keyNode)
                ->raw(', ')
                    ->write("\$_deferred")
                ->raw(");\n");
        }
    }

}