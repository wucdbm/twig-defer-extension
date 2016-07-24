<?php

namespace Wucdbm\Extension\Twig\Node;

class DeferNode extends \Twig_Node {

    protected $value;

    public function __construct($name, \Twig_Node $value, $line, $tag = null) {
        $this->value = $value;
        parent::__construct(array('value' => $value), array('name' => $name), $line, $tag);
    }

    public function compile(\Twig_Compiler $compiler) {
        if ($this->value instanceof \Twig_Node_Expression) {
            $compiler
                ->addDebugInfo($this)
                ->write('$this->env->getExtension(\'defer\')->defer(')
                ->string($this->getAttribute('name'))
                ->raw(', ')
                ->subcompile($this->getNode('value'))
                ->raw(");\n");
        } else {
            // This is very ugly, but I don't know how else to handle \Twig_Node that compiles into this: echo "constant expression";
            $compiler
                ->write("\$deferred = function() use (\$context, \$blocks) {")
                ->indent()
                ->write("ob_start();\n")
                ->write("try {\n")
                ->indent()
                ->subcompile($this->getNode('value'))
                ->write("return ob_get_clean();")
                ->outdent()
                ->write("} catch (Exception \$e) {\n")
                ->indent()
                ->write("ob_end_clean();\n\n")
                ->write("throw \$e;\n")
                ->outdent()
                ->write("} catch (Throwable \$e) {\n")
                ->indent()
                ->write("ob_end_clean();\n\n")
                ->write("throw \$e;\n")
                ->outdent()
                ->write("}\n\n")
                ->outdent()
                ->write("};");

            $compiler
                ->addDebugInfo($this)
                ->write('$this->env->getExtension(\'defer\')->defer(')
                ->string($this->getAttribute('name'))
                ->raw(', ')
                ->write("\$deferred()")
                ->raw(");\n");
        }
    }

}