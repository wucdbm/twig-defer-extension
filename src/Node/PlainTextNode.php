<?php

namespace Wucdbm\Extension\Twig\Node;

use Twig\Compiler;
use Twig\Node\Node;

class PlainTextNode extends Node {

    public function __construct($data, $lineno) {
        parent::__construct(array(), array('data' => $data), $lineno);
    }

    public function compile(Compiler $compiler) {
        $compiler
            ->addDebugInfo($this)
            ->string($this->getAttribute('data'));
    }

}