<?php

namespace ApiRecipe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiRecipe\Manager\RecipeManager;
use ApiRecipe\Manager\YmlParserTrait;

class SuggestRecipeCommand extends Command
{
    use YmlParserTrait;

    /**
     * @var RecipeManager
     */
    protected $recipeManager;

    protected function configure()
    {
        $this
            ->setName('recipes:suggest')
            ->setDescription('Suggest a recipe to exec')
            ->addArgument('suggester', InputArgument::OPTIONAL, 'The suggester to use')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $suggesterName = $input->getArgument('suggester');

        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);
        $suggesters = isset($config['suggesters']) ? $config['suggesters'] : [];
        $io = new SymfonyStyle($input, $output);
        $recipes = [];
        foreach ($suggesters as $suggester => $suggesterParams) {
            if (null === $suggesterName || $suggesterName === $suggester) {
                $suggesterClass = $suggesterParams['class'];
                $arguments = $suggesterParams['arguments'];
                $suggester = new $suggesterClass($arguments);
                $recipes = array_merge($recipes, $suggester->suggest());
            }
        }

        if (0 !== count($recipes)) {
            $io->success(sprintf('Probably %s', $recipes[0]->getTitle()));
        } else {
            $io->error('Nothing to suggest');
        }
    }
}
