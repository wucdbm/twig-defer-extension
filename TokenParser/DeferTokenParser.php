<?php

namespace Wucdbm\Extension\Twig\TokenParser;

use Wucdbm\Extension\Twig\Extension\DeferExtension;
use Wucdbm\Extension\Twig\Node\DeferNode;
use Wucdbm\Extension\Twig\Node\PlainTextNode;

class DeferTokenParser extends \Twig_TokenParser {

    public function parse(\Twig_Token $token) {
        $line = $token->getLine();
        $parser = $this->parser;
        $stream = $parser->getStream();

        if ($stream->test(\Twig_Token::STRING_TYPE)) {
            $expected = $stream->expect(\Twig_Token::STRING_TYPE);
            $name = $expected->getValue();
            $nameNode = new PlainTextNode($name, $expected->getLine());
        } else {
            $expected = $stream->expect(\Twig_Token::NAME_TYPE);
            $name = $expected->getValue();
            $nameNode = new \Twig_Node_Expression_Name($name, $expected->getLine());
        }

        if ($stream->nextIf(\Twig_Token::BLOCK_END_TYPE)) {
            $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            if ($token = $stream->nextIf(\Twig_Token::NAME_TYPE)) {
                $value = $token->getValue();

                if ($value != $name) {
                    throw new \Twig_Error_Syntax(sprintf('Expected enddefer for block "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getFilename());
                }
            }
        } else {
            $body = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new DeferNode($nameNode, $body, $line, $this->getTag());
    }

    public function decideBlockEnd(\Twig_Token $token) {
        return $token->test('enddefer');
    }

    public function getTag() {
        return 'defer';
    }

}