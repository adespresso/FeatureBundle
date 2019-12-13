<?php

namespace Ae\FeatureBundle\Twig\TokenParser;

use Ae\FeatureBundle\Twig\Node\FeatureNode;
use Twig_Error_Syntax;
use Twig_Token;
use Twig_TokenParser;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureTokenParser extends Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $name = null;
        $parent = null;
        if (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(Twig_Token::STRING_TYPE)) {
                // {% feature "name" %}
                $name = $stream->next()->getValue();
            } elseif (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
                throw new Twig_Error_Syntax('Unexpected token. Twig was looking for the "name" string.');
            }
            if ($stream->test('from')) {
                // {% feature "name" from "parent" %}
                $stream->next();
                $parent = $stream->next()->getValue();
            } elseif (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
                throw new Twig_Error_Syntax('Unexpected token. Twig was looking for the "from" keyword.');
            }
        }

        // {% feature %}...{% endfeature %}
        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideFeatureFork']);

        $else = null;
        $end = false;
        while (!$end) {
            switch ($this->parser->getStream()->next()->getValue()) {
                case 'else':
                    $stream->expect(Twig_Token::BLOCK_END_TYPE);
                    $else = $this->parser->subparse([$this, 'decideFeatureEnd']);
                    break;

                case 'endfeature':
                    $end = true;
                    break;

                default:
                    throw new Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tags "else" or "endfeature" to close the "feature" block started at line %d)', $lineno), -1);
            }
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new FeatureNode(
            $name,
            $parent,
            $body,
            $else,
            $lineno,
            $this->getTag()
        );
    }

    public function decideFeatureFork(Twig_Token $token)
    {
        return $token->test(['else', 'endfeature']);
    }

    public function decideFeatureEnd(Twig_Token $token)
    {
        return $token->test(['endfeature']);
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'feature';
    }
}
