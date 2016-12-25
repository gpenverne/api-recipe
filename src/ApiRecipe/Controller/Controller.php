<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\ProviderManager;
use ApiRecipe\Manager\RecipeManager;
use Symfony\Component\HttpFoundation\Request;
use ApiRecipe\Manager\YmlParserTrait;

class Controller implements ControllerInterface
{
    use YmlParserTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        $this->request->query->set('format', $responseFormat);

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
}
