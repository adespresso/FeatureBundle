<?php

namespace Ae\FeatureBundle\Tests\Twig\Node;

use Ae\FeatureBundle\Twig\Node\FeatureNode;
use Twig_Node;
use Twig_Node_Expression_Name;
use Twig_Node_Print;
use Twig_Test_NodeTestCase;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 * @covers \Ae\FeatureBundle\Twig\Node\FeatureNode
 */
class FeatureNodeTest extends Twig_Test_NodeTestCase
{
    public function testConstructor()
    {
        $name = 'foo';
        $parent = 'parent';
        $body = new Twig_Node([], [], 1);

        $node = new FeatureNode($name, $parent, $body, null, 1);
        $tests = $node->getNode('tests')->getIterator();

        $this->assertInstanceOf('Twig_Node_Expression_MethodCall', $tests[0]);
        $this->assertEquals($body, $tests[1]);

        // if not defined, the "else" node won't be created
        // see https://github.com/twigphp/Twig/pull/2123
        // TO DO: remove after twig/twig:^1.25.0
        if ($node->hasNode('else')) {
            $this->assertNull($node->getNode('else'));

            return;
        }

        $this->assertFalse($node->hasNode('else'));
    }

    /**
     * @dataProvider getTests
     */
    public function testCompile(
        $node,
        $source,
        $environment = null,
        $isPattern = null
    ) {
        parent::testCompile($node, $source, $environment, $isPattern);
    }

    public function getTests()
    {
        $tests = [];

        $name = 'foo';
        $parent = 'parent';
        $body = new Twig_Node([
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 1), 1),
        ], [], 1);

        $node = new FeatureNode($name, $parent, $body, null, 1);
        $tests[] = [$node, <<<EOF
// line 1
if (\$this->env->getExtension('feature')->isGranted("$name", "$parent")) {
    echo {$this->getVariableGetter('foo')};
}
EOF
        ];

        $else = new Twig_Node([
            new Twig_Node_Print(new Twig_Node_Expression_Name('bar', 1), 1),
        ], [], 1);
        $node = new FeatureNode($name, $parent, $body, $else, 1);
        $tests[] = [$node, <<<EOF
// line 1
if (\$this->env->getExtension('feature')->isGranted("$name", "$parent")) {
    echo {$this->getVariableGetter('foo')};
} else {
    echo {$this->getVariableGetter('bar')};
}
EOF
        ];

        return $tests;
    }
}
