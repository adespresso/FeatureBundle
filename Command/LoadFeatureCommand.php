<?php

namespace Ae\FeatureBundle\Command;

use Ae\FeatureBundle\Twig\Node\FeatureNode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Twig_Node;
use Twig_Source;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class LoadFeatureCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('features:load')
            ->setDescription('Persist new features found in templates')
            ->addArgument(
                'path',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'The path or bundle where to load the features'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Do not persist new features'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $twig = $container->get('twig');
        $files = $this->getFinderInstance($input->getArgument('path'));

        $found = [];
        foreach ($files as $file) {
            if (class_exists(Twig_Source::class)) {
                $tree = $twig->parse($twig->tokenize(new Twig_Source(
                    file_get_contents($file->getPathname()),
                    $file->getFilename(),
                    $file->getPathname()
                )));
            } else {
                $tree = $twig->parse(
                    $twig->tokenize(file_get_contents($file->getPathname()))
                );
            }

            $tags = $this->findFeatureNodes($tree);

            if (empty($tags)) {
                continue;
            }

            $found = array_merge($found, $tags);

            foreach ($tags as $tag) {
                $output->writeln(sprintf(
                    'Found <info>%s</info>.<info>%s</info> in <info>%s</info>',
                    $tag['parent'],
                    $tag['name'],
                    $file->getFilename()
                ));
            }
        }

        if ($input->getOption('dry-run')) {
            return 0;
        }

        $manager = $container->get('ae_feature.manager');
        foreach ($found as $tag) {
            $manager->findOrCreate($tag['name'], $tag['parent']);
        }

        return 0;
    }

    /**
     * Find feature nodes.
     *
     * @return array
     */
    private function findFeatureNodes(Twig_Node $node)
    {
        $found = [];
        $stack = [$node];
        while ($stack) {
            $node = array_pop($stack);
            if ($node instanceof FeatureNode) {
                $arguments = $node
                    ->getNode('tests')
                    ->getNode(0)
                    ->getNode('arguments')
                    ->getKeyValuePairs();

                $tag = [];
                foreach ($arguments as $argument) {
                    $keyAttr = $argument['key']->getAttribute('value');
                    $valueAttr = $argument['value']->getAttribute('value');

                    $tag[$keyAttr] = $valueAttr;
                }
                $key = md5(serialize($tag));
                $found[$key] = $tag;
            } else {
                foreach ($node as $child) {
                    if (null !== $child) {
                        $stack[] = $child;
                    }
                }
            }
        }

        return array_values($found);
    }

    /**
     * Gets a Finder instance with required paths.
     *
     * @param array $dirsOrBundles Required directories or bundles
     *
     * @return Finder
     */
    private function getFinderInstance(array $dirsOrBundles)
    {
        $finder = new Finder();
        $application = $this->getApplication();

        $kernel = null;
        $bundles = [];
        if ($application instanceof Application) {
            $kernel = $application->getKernel();
            $bundles = $kernel->getBundles();
        }

        foreach ($dirsOrBundles as $dirOrBundle) {
            if (null !== $kernel && isset($bundles[$dirOrBundle])) {
                $bundle = $kernel->getBundle($dirOrBundle);
                $dirOrBundle = $bundle->getPath().'/Resources/views/';
            }

            $finder->in($dirOrBundle);
        }

        return $finder
            ->files()
            ->name('*.twig');
    }
}
