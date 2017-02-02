<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\ProviderManager;
use ApiRecipe\Manager\RecipeManager;
use ApiRecipe\Manager\YmlParserTrait;
use Symfony\Component\DependencyInjection\Container;

class Controller implements ControllerInterface
{
    use YmlParserTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $recipeName
     *
     * @return RecipeManager
     */
    public function getRecipeManager($recipeName = null)
    {
        return new RecipeManager($recipeName);
    }

    /**
     * @param string $responseFormat
     *
     * @return $this
     */
    protected function setResponseFormat($responseFormat)
    {
        $this->container->get('request')->query->set('format', $responseFormat);

        return $this;
    }

    /**
     * @param string $providerName
     *
     * @return ProviderManager
     */
    protected function getProvider($providerName)
    {
        $providerManager = new ProviderManager();

        return $providerManager->getProvider($providerName);
    }

    /**
     * @param string $category
     *
     * @return array
     */
    protected function getConfig($category = null)
    {
        if (null === $this->config) {
            $file = sprintf('%s/../../../app/config/config.yml', __DIR__);
            $this->config = $this->parseYmlFile($file);
        }
        if (null !== $category) {
            if (!isset($this->config[$category])) {
                return [];
            }

            return $this->config[$category];
        }

        return $this->config;
    }

    /**
     * @param  string $method
     * @param  array $args
     *
     * @return mixed
     */
    public function __call($method, $args = [])
    {
        if ('get' == $method) {
            return $this->container->get($args[0]);
        }
    }
}
