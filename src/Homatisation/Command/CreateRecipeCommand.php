<?php

namespace Homatisation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Homatisation\Manager\RecipeManager;
use Doctrine\Common\Inflector\Inflector;

class CreateRecipeCommand extends Command
{
    /**
     * @var RecipeManager
     */
    protected $recipeManager;

    protected function configure()
    {
        $this
            ->setName('recipes:create')
            ->setDescription('Create a recipe')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $recipe = new \stdClass();

        $io = new SymfonyStyle($input, $output);
        $recipe->title = $io->ask('Title of the recipe ?');
        $recipeFile = sprintf('%s/../../../recipes/%s.yml', __DIR__, Inflector::camelize($recipe->title));
        $recipe->description = $io->ask('Description of the recipe ?');
        $recipe->description = $io->ask('Picture of the recipe ?');
        $recipe->actions = [
            'on' => [],
            'off' => [],
            'each_time' => [],
        ];

        $sections = array_keys($recipe->actions);

        foreach ($sections as $section) {
            $io->section(sprintf('Actions "%s"', $section));
            while ($io->confirm('Add an action?')) {
                $action = $this->generateAction($io);
                $recipe->actions[$section][] = $action;
            }
        }

        var_dump($recipe);
        $io->success(sprintf('Recipe %s created', $recipe->title));
    }

    protected function generateAction($io)
    {
        $provider = $io->ask('Provider for this action ?');
        $method = $io->ask('Action call on this provider ?');
        $argument = $io->ask('Any argument ?');

        $action = sprintf('%s:%s', $provider, $method);
        if (null != $argument) {
            $action .= sprintf(':%s', $argument);
        }

        return $action;
    }
}
