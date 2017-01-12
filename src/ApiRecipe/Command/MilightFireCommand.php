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

    protected $exitFile = '/tmp/milight.pid';

    protected $lockFile = '/tmp/fire.lock';

    protected function configure()
    {
        $this
            ->setName('milight:fire')
            ->setDescription('Simulate fire colors on milight')
            ->addArgument('group', InputArgument::OPTIONAL, 'Milight group')
            ->addArgument('background', InputArgument::OPTIONAL, 'Run in background?', false)
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $group = $input->getArgument('group');
        if (is_file($this->lockFile)) {
            return;
        }
        if ($input->getArgument('background')) {
            echo '/usr/bin/php '.__DIR__.'/../../../bin/console milight:fire '.$group.' 0 > /dev/null 2>&1 &';

            return exec('/usr/bin/php '.__DIR__.'/../../../bin/console milight:fire '.$group.' 0 > /dev/null 2>&1 &');
        }

        touch($this->lockFile);

        if (is_file($this->exitFile)) {
            unlink($this->exitFile);
        }

        $io = new SymfonyStyle($input, $output);
        $milight = $this->getMilightProvider();
        $milight->rgbwSetColorToOrange($group);

        $currLum = $futurLum = 60;
        $i = 0;
        while (!is_file($this->exitFile)) {
            ++$i;

            if ($i % 2) {
                $futurLum += rand(15, 40);
                $milight->rgbwBrightnessPercent($currLum + 5, $group);
            } else {
                $futurLum -= rand(15, 40);
                $milight->rgbwBrightnessPercent($currLum - 5, $group);
            }

            if ($futurLum < 4) {
                $futurLum = 4;
            } elseif ($futurLum > 96) {
                $futurLum = 96;
            }

            $io->comment(sprintf('Lum to %d', $futurLum));
            if ($futurLum < $currLum) {
                $action = 'down';
            } else {
                $action = 'up';
            }

            while (('down' === $action && $futurLum < $currLum) || ('up' === $action && $futurLum > $currLum)) {
                if ('down' === $action) {
                    $currLum -= 10;
                } else {
                    $currLum += 10;
                }
                if ($currLum > 100) {
                    $currLum = 100;
                }
                if ($currLum < 0) {
                    $currLum = 1;
                }
                $milight->rgbwBrightnessPercent($currLum, $group);
            }
        }
        unlink($this->lockFile);
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
