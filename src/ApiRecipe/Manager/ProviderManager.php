<?php

namespace ApiRecipe\Manager;

use Doctrine\Common\Inflector\Inflector;
use ApiRecipe\Provider\ProviderInterface;

class ProviderManager implements ManagerInterface
{
    use YmlParserTrait;

    protected $providers = [];

    /**
     * @param string $providerName
     *
     * @return ProviderInterface
     */
    public function getProvider($providerName)
    {
        $knownProviderName = $providerName;
        if (isset($this->providers[$knownProviderName])) {
            return $this->providers[$knownProviderName];
        }

        $providerName = Inflector::Camelize($providerName);
        $config = $this->getConfig($providerName);
        if (empty($config)) {
            throw new \Exception(sprintf('Provider "%s" not found.', $providerName));
        }
        $providerName = $config['provider'];
        $expectedClass = sprintf('ApiRecipe\\Provider\\%sProvider', ucfirst($providerName));

        $this->providers[$knownProviderName] = new $expectedClass($config, $knownProviderName);

        return $this->providers[$knownProviderName];
    }

    public function getCurrentProviderName()
    {
        return $this->currentProviderName;
    }

    /**
     * @return $this
     */
    protected function getConfig($providerName = null)
    {
        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);

        if (null === $providerName) {
            return $config['providers'];
        }

        foreach ($config['providers'] as $key => $providerConfig) {
            if (Inflector::Camelize($key) === $providerName) {
                return $providerConfig;
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function listProviders()
    {
        $providers = $this->getConfig();
        $providers = array_map(function ($provider) {
            return $provider['provider'];
        }, $providers);

        return array_values($providers);
    }

    /**
     * @param string $providerName
     *
     * @return array
     */
    public function getActions($providerName)
    {
        $provider = $this->getProvider($providerName);
        if (method_exists($provider, 'getActions')) {
            return $provider->getActions();
        }

        return [];
    }
}
