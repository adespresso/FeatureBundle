<?php

namespace Ae\FeatureBundle\Tests\Twig\Node;

use Ae\FeatureBundle\Twig\Node\FeatureNode;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers Ae\FeatureBundle\Twig\Node\FeatureNode
 */
class FeatureNodeTest extends \Twig_Test_NodeTestCase
{
    public function testConstructor()
    {
        $name   = 'foo';
        $parent = 'parent';
        $body   = new \Twig_Node(array(), array(), 1);

        $node   = new FeatureNode($name, $parent, $body, null, 1);
        $tests  = $node->getNode('tests')->getIterator();

        $this->assertInstanceOf('Twig_Node_Expression_MethodCall', $tests[0]);
        $this->assertEquals($body, $tests[1]);
        $this->assertNull($node->getNode('else'));
    }

    /**
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null, $isPattern = null)
    {
        parent::testCompile($node, $source, $environment, $isPattern);
    }

    public function getTests()
    {
        $tests = array();

        $name   = 'foo';
        $parent = 'parent';
        $body   = new \Twig_Node(array(
            new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
        ), array(), 1);

        $node = new FeatureNode($name, $parent, $body, null, 1);
        $tests[] = array($node, <<<EOF
// line 1
if (\$this->env->getExtension('feature')->isGranted("$name", "$parent")) {
    echo {$this->getVariableGetter('foo')};
}
EOF
        );

        $else = new \Twig_Node(array(
            new \Twig_Node_Print(new \Twig_Node_Expression_Name('bar', 1), 1),
        ), array(), 1);
        $node = new FeatureNode($name, $parent, $body, $else, 1);
        $tests[] = array($node, <<<EOF
// line 1
if (\$this->env->getExtension('feature')->isGranted("$name", "$parent")) {
    echo {$this->getVariableGetter('foo')};
} else {
    echo {$this->getVariableGetter('bar')};
}
EOF
        );

        return $tests;
    }
}
