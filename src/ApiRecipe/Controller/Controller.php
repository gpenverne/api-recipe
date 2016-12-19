<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\RecipeManager;
use Symfony\Component\HttpFoundation\Request;

class Controller implements ControllerInterface
{
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
}
