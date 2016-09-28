<?php

namespace Ae\FeatureBundle\Command;

use Ae\FeatureBundle\Entity\FeatureManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command used to create a feature.
 *
 * @author Emanuele Minotto <emanuele@adespresso.com>
 */
class CreateFeatureCommand extends Command
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
     * @param FeatureManager $featureManager
     * @param EntityManager  $entityManager
     */
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
            ->setName('features:create')
            ->setDescription('Create a new feature')
            ->addArgument('parent', InputArgument::REQUIRED, 'Parent feature')
            ->addArgument('name', InputArgument::REQUIRED, 'Feature name')
            ->addOption(
                'enabled',
                null,
                InputOption::VALUE_NONE,
                'The feature will be created as enabled'
            )
            ->addOption(
                'role',
                null,
                InputOption::VALUE_REQUIRED,
                'The feature will be only for a role'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $parent = $input->getArgument('parent');

        $output->write(sprintf(
            'Creating <info>%s</info>.<info>%s</info>... ',
            $parent,
            $name
        ));

        $feature = $this->featureManager->findOrCreate($name, $parent);

        $feature->setEnabled($input->getOption('enabled'));
        $feature->setRole($input->getOption('role'));

        $this->entityManager->persist($feature);
        $this->entityManager->flush();

        $this->featureManager->emptyCache($name, $parent);

        $output->writeln('OK');
    }
}
