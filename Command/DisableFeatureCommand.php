<?php

namespace Ae\FeatureBundle\Command;

use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to disable an existing feature.
 *
 * @author Emanuele Minotto <emanuele@adespresso.com>
 */
class DisableFeatureCommand extends Command
{
    /**
     * @var FeatureManager
     */
    private $featureManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        FeatureManager $featureManager,
        EntityManager $entityManager
    ) {
        parent::__construct();

        $this->featureManager = $featureManager;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('features:disable')
            ->setDescription('Disable an existing feature')
            ->addArgument('parent', InputArgument::REQUIRED, 'Parent feature')
            ->addArgument('name', InputArgument::REQUIRED, 'Feature name');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $parent = $input->getArgument('parent');

        $output->write(sprintf(
            'Disabling <info>%s</info>.<info>%s</info>... ',
            $parent,
            $name
        ));

        $feature = $this->featureManager->find($name, $parent);

        $feature->setEnabled(false);

        $this->entityManager->persist($feature);
        $this->entityManager->flush();

        $this->featureManager->emptyCache($name, $parent);

        $output->writeln('OK');

        return 0;
    }
}
