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
     * @var ProviderManager
     */
    private $providerManager;

    /**
     * @param string      $providerName
     * @param string      $method
     * @param null|string $argument
     */
    public function __construct($providerName, $method, $argument = null)
    {
        $this->providerName = $providerName;
        $this->method = $method;
        $this->argument = $argument;
        $this->providerManager = new ProviderManager();
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

        return (bool) call_user_func_array([$provider, $method], explode(',', $argument));
    }
}
