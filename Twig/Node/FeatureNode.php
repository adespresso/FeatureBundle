<?php

namespace Ae\FeatureBundle\Twig\Node;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class FeatureNode extends \Twig_Node_If
{
    /**
     * @param int    $lineno
     * @param string $tag
     */
    public function __construct($name, $parent, $body, $else, $lineno, $tag = null)
    {
        $tests = new \Twig_Node(array(
            $this->createExpression($name, $parent, $lineno),
            $body,
        ));

        parent::__construct($tests, $else, $lineno, $tag);
    }

    /**
     * @param integer $lineno
     */
    protected function createExpression($name, $parent, $lineno)
    {
        return new \Twig_Node_Expression_MethodCall(
            new \Twig_Node_Expression_ExtensionReference('feature', $lineno),
            'isGranted',
            new \Twig_Node_Expression_Array(
                array(
                    new \Twig_Node_Expression_Constant('name', $lineno),
                    new \Twig_Node_Expression_Constant($name, $lineno),
                    new \Twig_Node_Expression_Constant('parent', $lineno),
                    new \Twig_Node_Expression_Constant($parent, $lineno),
                ),
                $lineno
            ),
            $lineno
        );
    }
}
