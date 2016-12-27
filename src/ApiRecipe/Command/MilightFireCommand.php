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

        $currLum = $futurLum = 40;
        $i = 0;
        while (true) {
            ++$i;

            if ($i % 2) {
                $futurLum += rand(0, 30);
            } else {
                $futurLum -= rand(0, 30);
            }

            if ($futurLum < 4) {
                $futurLum = 4;
            } elseif ($futurLum > 96) {
                $futurLum = 96;
            }

            $io->comment(sprintf('Lum to %d', $futurLum));
            while ($currLum != $futurLum) {
                if ($futurLum < $currLum) {
                    $currLum -= 1;
                } else {
                    $currLum += 1;
                }
                $milight->rgbwBrightnessPercent($currLum, $group);
            }
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
