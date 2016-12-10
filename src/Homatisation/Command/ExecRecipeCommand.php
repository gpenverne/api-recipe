<?php

namespace Homatisation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Homatisation\Manager\RecipeManager;

class ExecRecipeCommand extends Command
{
    const FORMAT_JSON = 'json';

    const FORMAT_CONSOLE = 'console';

    /**
     * @var RecipeManager
     */
    protected $recipeManager;

    protected function configure()
    {
        $this
            ->setName('homatisation:recipe:exec')
            ->setDescription('Exec a recipe')
            ->addArgument('recipe', InputArgument::REQUIRED, 'The recipe name to exec')
            ->addArgument('state', InputArgument::OPTIONAL, 'Expected target state: On/Off')
            ->addArgument('format', InputArgument::OPTIONAL, 'Format: json or console')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $recipe = $input->getArgument('recipe');
        $state = $input->getArgument('state');

        $recipeManager = $this->getRecipeManager($recipe);

        $io = new SymfonyStyle($input, $output);
        $io->text(sprintf('Exec recipe %s...', $recipe));
        $io->success('Done.');
    }

    /**
     * @return RecipeManager
     */
    protected function getRecipeManager($recipeName)
    {
        if (null !== $this->recipeManager) {
            return $this->recipeManager;
        }

        $this->recipeManager = new RecipeManager($recipeName);

        return $this->recipeManager;
    }
}
