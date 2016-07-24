<?php

namespace Wucdbm\Extension\Twig\TokenParser;

use Wucdbm\Extension\Twig\Node\DeferredNode;

class DeferredTokenParser extends \Twig_TokenParser {

    public function parse(\Twig_Token $token) {
        $parser = $this->parser;
        $stream = $parser->getStream();

        if ($stream->test(\Twig_Token::NAME_TYPE)) {
            $name = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
        } else {
            $name = '_default';
        }

        $default = null;
        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            $default = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new DeferredNode($name, $default, $token->getLine(), $this->getTag());
    }

    public function getTag() {
        return 'deferred';
    }

}