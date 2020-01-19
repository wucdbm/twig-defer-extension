<?php

namespace Wucdbm\Extension\Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;
use Wucdbm\Extension\Twig\Extension\DeferExtension;

class DeferNode extends Node {

    /** @var Node */
    protected $keyNode;

    /** @var Node */
    protected $value;

    public function __construct(Node $keyNode, Node $value, $line, $tag = null) {
        $this->keyNode = $keyNode;
        $this->value = $value;
        parent::__construct([], [], $line, $tag);
    }

    public function compile(Compiler $compiler) {
        if ($this->value instanceof AbstractExpression) {
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