<?php

namespace ApiRecipe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiRecipe\Manager\RecipeManager;
use ApiRecipe\Manager\YmlParserTrait;

class SetupCollectorCommand extends Command
{
    const STATE_TOGGLE = 'toggle';

    use YmlParserTrait;
    /**
     * @var RecipeManager
     */
    protected $recipeManager;

    protected function configure()
    {
        $this
            ->setName('collectors:setup')
            ->setDescription('Set up a collector')
            ->addArgument('collector', InputArgument::OPTIONAL, 'The collector name to set up')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $collectorName = $input->getArgument('collector');

        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);
        $collectors = isset($config['collectors']) ? $config['collectors'] : [];
        $io = new SymfonyStyle($input, $output);
        foreach ($collectors as $collector => $collectorParams) {
            if (null === $collectorName || $collectorName === $collector) {
                $io->text(sprintf('Setup %s', $collectorName));
                $collectorClass = $collectorParams['class'];
                $arguments = $collectorParams['arguments'];
                $collector = new $collectorClass($arguments);
                $collector->setup();
            }
        }
        $io->success('Done.');
    }
}
