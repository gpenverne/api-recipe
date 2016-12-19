<?php

namespace ApiRecipe\Controller;

class RecipesController extends Controller
{
    /**
     * @return array
     */
    public function indexAction()
    {
        $this->setResponseFormat('json');

        return $this->getRecipManager()->getAll();
    }

    /**
     * @param null|string $recipeName
     *
     * @return \stdClass
     */
    public function showAction($recipeName = null)
    {
        $this->setResponseFormat('json');

        return $this->getRecipManager()->get($recipeName);
    }

    /**
     * @param string] $recipeName
     *
     * @return array
     */
    public function execAction($recipeName)
    {
        $this->setResponseFormat('json');

        $state = $this->request->get('state');

        return $this->getRecipManager($recipeName)->exec($state);
    }
}
