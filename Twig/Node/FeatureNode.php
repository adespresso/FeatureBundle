<?php

namespace Ae\FeatureBundle\Twig\Node;

use Twig_Node;
use Twig_Node_Expression_Array;
use Twig_Node_Expression_Constant;
use Twig_Node_Expression_ExtensionReference;
use Twig_Node_Expression_MethodCall;
use Twig_Node_If;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureNode extends Twig_Node_If
{
    public function __construct($name, $parent, $body, $else, $lineno, $tag = null)
    {
        $tests = new Twig_Node([
            $this->createExpression($name, $parent, $lineno),
            $body,
        ]);

        parent::__construct($tests, $else, $lineno, $tag);
    }

    protected function createExpression($name, $parent, $lineno)
    {
        return new Twig_Node_Expression_MethodCall(
            new Twig_Node_Expression_ExtensionReference('feature', $lineno),
            'isGranted',
            new Twig_Node_Expression_Array(
                [
                    new Twig_Node_Expression_Constant('name', $lineno),
                    new Twig_Node_Expression_Constant($name, $lineno),
                    new Twig_Node_Expression_Constant('parent', $lineno),
                    new Twig_Node_Expression_Constant($parent, $lineno),
                ],
                $lineno
            ),
            $lineno
        );
    }
}
