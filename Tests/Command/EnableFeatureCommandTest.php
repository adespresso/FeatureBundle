<?php

namespace Ae\FeatureBundle\Tests\Command;

use Ae\FeatureBundle\Command\EnableFeatureCommand;
use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Emanuele Minotto <emanuele@adespresso.com>
 * @covers \Ae\FeatureBundle\Command\EnableFeatureCommand
 */
class EnableFeatureCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FeatureManager
     */
    private $featureManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EnableFeatureCommand
     */
    private $command;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->featureManager = $this
            ->getMockBuilder(FeatureManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->command = new EnableFeatureCommand(
            $this->featureManager,
            $this->entityManager
        );
    }

    public function testExecute()
    {
        $commandTester = new CommandTester($this->command);

        $parent = sha1(mt_rand());
        $name = sha1(mt_rand());

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature
            ->expects($this->once())
            ->method('setEnabled')
            ->with(true);

        $feature
            ->expects($this->never())
            ->method('setRole');

        $this->featureManager
            ->expects($this->once())
            ->method('find')
            ->with($name, $parent)
            ->willReturn($feature);

        $this->featureManager
            ->expects($this->once())
            ->method('emptyCache')
            ->with($name, $parent);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Feature::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $code = $commandTester->execute([
            'parent' => $parent,
            'name' => $name,
        ]);

        $this->assertSame(0, $code);
        $this->assertSame(
            'Enabling '.$parent.'.'.$name.'... OK',
            trim($commandTester->getDisplay())
        );
    }

    public function testExecuteWithOption()
    {
        $commandTester = new CommandTester($this->command);

        $parent = sha1(mt_rand());
        $name = sha1(mt_rand());
        $role = sha1(mt_rand());

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature
            ->expects($this->once())
            ->method('setEnabled')
            ->with(true);

        $feature
            ->expects($this->once())
            ->method('setRole')
            ->with($role);

        $this->featureManager
            ->expects($this->once())
            ->method('find')
            ->with($name, $parent)
            ->willReturn($feature);

        $this->featureManager
            ->expects($this->once())
            ->method('emptyCache')
            ->with($name, $parent);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Feature::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $code = $commandTester->execute([
            'parent' => $parent,
            'name' => $name,
            '--role' => $role,
        ]);

        $this->assertSame(0, $code);
        $this->assertSame(
            'Enabling '.$parent.'.'.$name.'... OK',
            trim($commandTester->getDisplay())
        );
    }
}
