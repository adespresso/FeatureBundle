<?php

namespace Ae\FeatureBundle\Command;

use Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Ae\FeatureBundle\Twig\Node\FeatureNode;

/**
 * @author Carlo Forghieri <carlo@adespresso.com>
 */
class LoadFeatureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('features:load')
            ->setDescription('Persist new features found in templates')
            ->setDefinition(array(
                new InputArgument('bundle', InputArgument::REQUIRED, 'The bundle where to load the features'),
                new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Do not persist new features'),
            ));
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $twig   = $this->getContainer()->get('twig');
        $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument('bundle'));
        if (!$bundle) {
            new \InvalidArgumentException("Bundle `$bundle` does not exists");
        }
        $found   = array();
        $dir = $bundle->getPath().'/Resources/views/';
        if (!is_dir($dir)) {
            throw \Exception("'Directory `$dir` does not exists.");
        }
        $finder = new Finder();
        $files  = $finder->files()->name('*.html.twig')->in($dir);
        foreach ($files as $file) {
            $tree = $twig->parse($twig->tokenize(file_get_contents($file->getPathname())));
            $tags = $this->findFeatureNodes($tree);
            if (!empty($tags)) {
                $found = array_merge($found, $tags);

                foreach ($tags as $tag) {
                    $output->writeln(sprintf('Found <info>%s</info>.<info>%s</info> in <info>%s</info>', $tag['parent'], $tag['name'], $file->getFilename()));
                }
            }
        }
        if ($input->getOption('dry-run')) {
            return;
        }
        $manager = $this->getContainer()->get('cw_feature.manager');
        foreach ($found as $tag) {
            $manager->findOrCreate($tag['name'], $tag['parent']);
        }
    }

    protected function findFeatureNodes(\Twig_Node $node)
    {
        $found = array();
        $stack = array($node);
        while ($stack) {
            $node = array_pop($stack);
            if ($node instanceof FeatureNode) {
                $arguments = $node->getNode('tests')->getNode(0)->getNode('arguments')->getKeyValuePairs();
                $tag = array();
                foreach ($arguments as $argument) {
                    $tag[$argument['key']->getAttribute('value')] = $argument['value']->getAttribute('value');
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
}
