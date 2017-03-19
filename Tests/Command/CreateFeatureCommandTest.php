<?php

namespace Ae\FeatureBundle\Tests\Command;

use Ae\FeatureBundle\Command\CreateFeatureCommand;
use Ae\FeatureBundle\Entity\Feature;
use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Emanuele Minotto <emanuele@adespresso.com>
 * @covers \Ae\FeatureBundle\Command\CreateFeatureCommand
 */
class CreateFeatureCommandTest extends PHPUnit_Framework_TestCase
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
     * @var CreateFeatureCommand
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

        $this->command = new CreateFeatureCommand(
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
            ->with(false);

        $feature
            ->expects($this->once())
            ->method('setRole')
            ->with(null);

        $this->featureManager
            ->expects($this->once())
            ->method('findOrCreate')
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
            'Creating '.$parent.'.'.$name.'... OK',
            trim($commandTester->getDisplay())
        );
    }

    public function testExecuteWithOptions()
    {
        $commandTester = new CommandTester($this->command);

        $parent = sha1(mt_rand());
        $name = sha1(mt_rand());
        $enabled = 1 === mt_rand(0, 1);
        $role = sha1(mt_rand());

        $feature = $this
            ->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feature
            ->expects($this->once())
            ->method('setEnabled')
            ->with($enabled);

        $feature
            ->expects($this->once())
            ->method('setRole')
            ->with($role);

        $this->featureManager
            ->expects($this->once())
            ->method('findOrCreate')
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
            '--enabled' => $enabled,
            '--role' => $role,
        ]);

        $this->assertSame(0, $code);
        $this->assertSame(
            'Creating '.$parent.'.'.$name.'... OK',
            trim($commandTester->getDisplay())
        );
    }
}
