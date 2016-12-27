<?php

namespace ApiRecipe\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ApiRecipe\Manager\RecipeManager;
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
    protected $milightProvider;

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
        $milight = $this->getMilightProvider();
        $milight->rgbwSetColorToOrange($group);

        $currLum = 50;
        $maxLum = 90;
        $i = 0;
        while (true) {
            ++$i;

            if ($i % 2 && $maxLum > $currLum) {
                $currLum += rand(0, 20);
            } else {
                $currLum -= rand(0, 20);
            }

            $io->comment(sprintf('Lum to %d', $currLum));
            $milight->rgbwBrightnessPercent($currLum, $group);
            $milight->rgbwBrightnessPercent($currLum - 1, $group);
            $milight->rgbwBrightnessPercent($currLum - 1, $group);
            $milight->rgbwBrightnessPercent($currLum - 1, $group);
            $milight->rgbwBrightnessPercent($currLum + 1, $group);
            $milight->rgbwBrightnessPercent($currLum + 1, $group);
            $milight->rgbwBrightnessPercent($currLum + 1, $group);
            sleep(2);
        }
    }

    /**
     * @return ProviderManager
     */
    protected function getMilightProvider()
    {
        if (null === $this->milightProvider) {
            $providerManager = new ProviderManager('milight');
            $this->milightProvider = $providerManager->getProvider('milight');
        }

        return $this->milightProvider;
    }
}
