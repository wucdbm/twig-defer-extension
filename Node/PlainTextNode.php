<?php

namespace Wucdbm\Extension\Twig\Node;

class PlainTextNode extends \Twig_Node {

    public function __construct($data, $lineno) {
        parent::__construct(array(), array('data' => $data), $lineno);
    }

    public function compile(\Twig_Compiler $compiler) {
        $compiler
            ->addDebugInfo($this)
            ->string($this->getAttribute('data'));
    }

}