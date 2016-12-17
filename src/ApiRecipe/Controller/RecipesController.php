<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\RecipeManager;

class RecipesController implements ControllerInterface
{
    public function indexAction()
    {
        return $this->getRecipManager()->getAll();
    }

    public function showAction($recipeName = null)
    {
        return $this->getRecipManager()->get($recipeName);
    }

    public function execAction($recipeName)
    {
        return $this->getRecipManager($recipeName)->exec();
    }

    private function getRecipManager($recipeName = null)
    {
        return new RecipeManager($recipeName);
    }
}
