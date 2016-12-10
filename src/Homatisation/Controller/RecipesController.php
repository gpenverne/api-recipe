<?php

namespace Homatisation\Controller;

use Homatisation\Manager\RecipeManager;

class RecipesController implements ControllerInterface
{
    public function indexAction()
    {
        return 'list';
    }

    public function execAction($recipeName)
    {
        $recipeManager = new RecipeManager($recipeName);

        return $recipeManager->exec();
    }
}
