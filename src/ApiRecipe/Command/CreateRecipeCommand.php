<?php

namespace ApiRecipe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiRecipe\Manager\RecipeManager;
use ApiRecipe\Manager\ProviderManager;
use Doctrine\Common\Inflector\Inflector;

class CreateRecipeCommand extends Command
{
    /**
     * @var RecipeManager
     */
    protected $recipeManager;

    /**
     * @var ProviderManager
     */
    protected $providerManager;

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
        $recipe->picture = $io->ask('Picture of the recipe ?');
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
                if (null !== $action) {
                    $recipe->actions[$section][] = $action;
                }
            }
        }

        $recipe = $this->generateYaml((array) $recipe)."\n";
        file_put_contents($recipeFile, $recipe);

        $io->success(sprintf('Recipe created in %s', realpath($recipeFile)));
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return string
     */
    protected function generateAction($io)
    {
        $providerManager = $this->getProviderManager();
        $providers = $providerManager->listProviders();
        $provider = $io->choice('Provider for this action ?', $providers);

        $methods = $providerManager->getActions($provider);

        if (empty($methods)) {
            $method = $io->ask('Action call on this provider ?');
        } else {
            $method = $io->choice('Action call on this provider ?', $methods);
        }

        $argument = $io->ask('Any argument ?');

        $action = sprintf('%s:%s', $provider, $method);
        if (null != $argument) {
            $action .= sprintf(':%s', $argument);
        }

        return $action;
    }

    protected function generateYaml($array, $level = -1)
    {
        $return = '';
        if (empty($array)) {
            return;
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $v = $this->generateYaml($v, $level + 1);
            }
            if (null === $v) {
                continue;
            }
            $return .= "\n";
            if (!is_numeric($k)) {
                $return .= $this->getTabs($level).sprintf('%s: %s', $k, $v);
            } else {
                $return .= $this->getTabs($level).sprintf('- %s', $v);
            }
        }

        return $return;
    }

    /**
     * @return ProviderManager
     */
    protected function getProviderManager()
    {
        if (null === $this->providerManager) {
            $this->providerManager = new ProviderManager();
        }

        return $this->providerManager;
    }

    /**
     * @param int $level
     *
     * @return string
     */
    private function getTabs($level = 0)
    {
        $tabs = '';
        for ($i = 0; $i <= $level; ++$i) {
            $tabs .= '    ';
        }

        return $tabs;
    }
}
