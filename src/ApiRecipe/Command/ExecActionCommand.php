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

class ExecActionCommand extends Command
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
            } else {
                $action = $actionFromCommandLine;
            }

            $io->text(sprintf('Exec action: %s', $action));
            $this->execAction($action);

            if (!$actionFromCommandLine) {
                $askForAction = $io->confirm('Exec an other action?');
            } else {
                $askForAction = false;
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
    protected function execAction($action, $state = null)
    {
        $actionManagerArguments = explode(':', $action);
        $actionManager = $this->getActionManager(
            $actionManagerArguments[0],
            $actionManagerArguments[1],
            $state,
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
    protected function getActionManager($providerName, $method, $state = null, $argument = null)
    {
        return new ActionManager($providerName, $method, $state, $argument);
    }
}
