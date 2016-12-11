<?php

namespace Homatisation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Homatisation\Manager\RecipeManager;
use Homatisation\Manager\ActionManager;
use Homatisation\Manager\ProviderManager;

class TestActionCommand extends Command
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
            ->setName('actions:exec')
            ->setDescription('Exec an action')
            ->addArgument('action', InputArgument::OPTIONAL, 'Action to exec (provider_name:provider_method:optional_argument)')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actionFromCommandLine = $input->getArgument('action');
        $recipe = new \stdClass();

        $io = new SymfonyStyle($input, $output);
        $askForAction = true;
        while ($askForAction) {
            if (!$actionFromCommandLine) {
                $action = $this->askForAction($io);
            }

            $io->text(sprintf('Exec action: %s', $action));
            $this->execAction($action);

            if (!$actionFromCommandLine) {
                $io->confirm('Exec an other action?');
            }
        }

        $io->success('Done.');
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return string
     */
    protected function askForAction($io)
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

    /**
     * @param string $action
     *
     * @return
     */
    protected function execAction($action)
    {
        $actionManagerArguments = explode(':', $action);
        $actionManager = $this->getActionManager(
            $actionManagerArguments[0],
            $actionManagerArguments[1],
            isset($actionManagerArguments[2]) ? $actionManagerArguments[2] : null
        );
        $actionManager->exec();
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
