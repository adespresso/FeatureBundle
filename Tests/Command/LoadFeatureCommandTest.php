<?php

namespace Ae\FeatureBundle\Tests\Command;

use Ae\FeatureBundle\Command\LoadFeatureCommand;
use Ae\FeatureBundle\Service\Feature;
use Ae\FeatureBundle\Twig\Extension\FeatureExtension;
use LogicException;
use PHPUnit_Framework_TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * @covers \Ae\FeatureBundle\Command\LoadFeatureCommand
 */
class LoadFeatureCommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->container = $this
            ->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->kernel
            ->method('getContainer')
            ->willReturn($this->container);
    }

    public function testExecuteEmpty()
    {
        $this->container
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap([
                ['twig', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, null],
                ['ae_feature.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, null],
            ]));

        $this->kernel
            ->method('getBundles')
            ->willReturn([]);

        $this->kernel
            ->method('getContainer')
            ->willReturn($this->container);

        $application = new Application($this->kernel);
        $application->add(new LoadFeatureCommand());

        $command = $application->find('features:load');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => [sys_get_temp_dir()],
        ]);

        $this->assertEmpty($commandTester->getDisplay());
    }

    public function testExecuteWithoutArguments()
    {
        $this->container
            ->expects($this->exactly(1))
            ->method('get')
            ->with('twig');

        $this->kernel
            ->method('getBundles')
            ->willReturn([]);

        $application = new Application($this->kernel);
        $application->add(new LoadFeatureCommand());

        $command = $application->find('features:load');
        $commandTester = new CommandTester($command);

        $this->setExpectedException(LogicException::class);
        $commandTester->execute([
            'command' => $command->getName(),
            'path' => [],
        ]);
    }

    public function testExecuteWithTemplates()
    {
        $service = $this
            ->getMockBuilder(Feature::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twig = new Twig_Environment(new Twig_Loader_Array([]));
        $twig->addExtension(new FeatureExtension($service));

        $this->container
            ->expects($this->exactly(1))
            ->method('get')
            ->with('twig')
            ->willReturn($twig);

        $this->kernel
            ->method('getBundles')
            ->willReturn([]);

        $application = new Application($this->kernel);
        $application->add(new LoadFeatureCommand());

        $command = $application->find('features:load');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
            'path' => [
                __DIR__.'/Fixtures',
            ],
            '--dry-run' => true,
        ]);

        $this->assertSame(
            'Found group.featureA in load_feature.twig',
            trim($commandTester->getDisplay())
        );
    }
}
