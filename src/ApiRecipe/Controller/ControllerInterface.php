<?php

namespace ApiRecipe\Controller;

interface ControllerInterface
{
    public function getRecipeManager($recipeName = null);
}
