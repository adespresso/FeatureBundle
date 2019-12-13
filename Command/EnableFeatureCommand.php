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
 * Command used to enable an existing feature.
 *
 * @author Emanuele Minotto <emanuele@adespresso.com>
 */
class EnableFeatureCommand extends Command
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
            ->setName('features:enable')
            ->setDescription('Enable an existing feature')
            ->addArgument('parent', InputArgument::REQUIRED, 'Parent feature')
            ->addArgument('name', InputArgument::REQUIRED, 'Feature name')
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
            'Enabling <info>%s</info>.<info>%s</info>... ',
            $parent,
            $name
        ));

        $feature = $this->featureManager->find($name, $parent);

        $feature->setEnabled(true);

        if (null !== $input->getOption('role')) {
            $feature->setRole($input->getOption('role'));
        }

        $this->entityManager->persist($feature);
        $this->entityManager->flush();

        $this->featureManager->emptyCache($name, $parent);

        $output->writeln('OK');
    }
}
