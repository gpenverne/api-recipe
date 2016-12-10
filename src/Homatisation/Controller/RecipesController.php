<?php

namespace Homatisation\Controller;

use Homatisation\Manager\RecipeManager;

class RecipesController implements ControllerInterface
{
    public function indexAction()
    {
        return $this->getRecipManager()->list();
    }

    public function showAction($recipeName = null)
    {
        return $this->getRecipManager()->list($recipeName);
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
