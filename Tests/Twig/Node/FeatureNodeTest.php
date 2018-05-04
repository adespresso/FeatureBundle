<?php

namespace Ae\FeatureBundle\Tests\Twig\Node;

use Ae\FeatureBundle\Twig\Extension\FeatureExtension;
use Ae\FeatureBundle\Twig\Node\FeatureNode;
use Twig_Environment;
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

    public function getTests()
    {
        $tests = [];

        $name = 'foo';
        $parent = 'parent';
        $body = new Twig_Node([
            new Twig_Node_Print(new Twig_Node_Expression_Name('foo', 1), 1),
        ], [], 1);

        $extension = version_compare(Twig_Environment::VERSION, '1.26.0', '>=')
            ? FeatureExtension::class
            : 'feature';

        $node = new FeatureNode($name, $parent, $body, null, 1);
        $tests[] = [$node, <<<EOF
// line 1
if (\$this->env->getExtension('{$extension}')->isGranted("$name", "$parent")) {
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
if (\$this->env->getExtension('{$extension}')->isGranted("$name", "$parent")) {
    echo {$this->getVariableGetter('foo')};
} else {
    echo {$this->getVariableGetter('bar')};
}
EOF
        ];

        return $tests;
    }
}
