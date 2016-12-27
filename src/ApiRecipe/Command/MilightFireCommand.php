<?php

namespace ApiRecipe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiRecipe\Manager\RecipeManager;
use ApiRecipe\Manager\ActionManager;
use ApiRecipe\Manager\ProviderManager;
use Symfony\Component\Console\Input\InputArgument;

class MilightFireCommand extends Command
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
            ->setName('actions:milight:fire')
            ->setDescription('Simulate fire colors on milight')
            ->addArgument('group', InputArgument::REQUIRED, 'Milight group')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getArgument('group');

        $io = new SymfonyStyle($input, $output);
        $milight = $this->getProviderManager('milight');
        $milight->rgbwSetColorToOrange($group);

        while (true) {
            $milight->rgbwBrightnessPercent(rand(40, 70), $group);
            sleep(3);
        }
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
     * @return ActionManager
     */
    protected function getActionManager($providerName, $method, $argument = null)
    {
        return new ActionManager($providerName, $method, $argument);
    }
}
