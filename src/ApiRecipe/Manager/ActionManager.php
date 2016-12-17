<?php

namespace ApiRecipe\Manager;

class ActionManager implements ManagerInterface
{
    /**
     * @var string
     */
    private $providerName;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $argument;

    /**
     * @var ProviderMAnager
     */
    private $providerManager;

    /**
     * @param string      $providerName
     * @param string      $method
     * @param null|string $argument
     */
    public function __construct($providerName, $method, $state = null, $argument = null)
    {
        $this->providerName = $providerName;
        $this->method = $method;
        $this->argument = $argument;
        $this->providerManager = new ProviderManager();
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getActionCommand()
    {
        if (null === $this->argument) {
            return sprintf('%s:%s', $this->providerName, $this->method);
        }

        return sprintf('%s:%s:%s', $this->providerName, $this->method, $this->argument);
    }
    /**
     * @return bool
     */
    public function exec()
    {
        $provider = $this->providerManager->getProvider($this->providerName);
        $method = $this->method;
        $argument = $this->argument;

        if (null === $argument) {
            return (bool) $provider->$method();
        }

        return (bool) $provider->$method($argument);
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
