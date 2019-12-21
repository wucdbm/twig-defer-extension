<?php

namespace Wucdbm\Extension\Twig\TokenParser;

use Twig\Error\SyntaxError;
use Twig\Node\Expression\NameExpression;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;
use Wucdbm\Extension\Twig\Node\DeferNode;
use Wucdbm\Extension\Twig\Node\PlainTextNode;

class DeferTokenParser extends AbstractTokenParser {

    public function parse(Token $token) {
        $line = $token->getLine();
        $parser = $this->parser;
        $stream = $parser->getStream();

        if ($stream->test(Token::STRING_TYPE)) {
            $expected = $stream->expect(Token::STRING_TYPE);
            $name = $expected->getValue();
            $nameNode = new PlainTextNode($name, $expected->getLine());
        } else {
            $expected = $stream->expect(Token::NAME_TYPE);
            $name = $expected->getValue();
            $nameNode = new NameExpression($name, $expected->getLine());
        }

        if ($stream->nextIf(Token::BLOCK_END_TYPE)) {
            $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            if ($token = $stream->nextIf(Token::NAME_TYPE)) {
                $value = $token->getValue();

                if ($value != $name) {
                    throw new SyntaxError(sprintf('Expected enddefer for block "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getFilename());
                }
            }
        } else {
            $body = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        return new DeferNode($nameNode, $body, $line, $this->getTag());
    }

    public function decideBlockEnd(Token $token) {
        return $token->test('enddefer');
    }

    public function getTag() {
        return 'defer';
    }

}