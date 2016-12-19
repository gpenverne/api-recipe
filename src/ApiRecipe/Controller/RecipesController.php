<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\RecipeManager;
use ApiRecipe\Manager\YmlParserTrait;

class RecipesController implements ControllerInterface
{
    use YmlParserTrait;

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

    public function suggestAction()
    {
        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);
        $suggesters = isset($config['suggesters']) ? $config['suggesters'] : [];
        $recipes = [];
        foreach ($suggesters as $suggester => $suggesterParams) {
            $suggesterClass = $suggesterParams['class'];
            $arguments = $suggesterParams['arguments'];
            $suggester = new $suggesterClass($arguments);
            $recipes = array_merge($recipes, $suggester->suggest());
        }

        return array_map(function ($recipeManager) {
            $infos = $recipeManager->getInfos();
            $probability = $recipeManager->getProbability();
            $infos->probability = $probability;

            return $infos;
        }, $recipes);
    }

    private function getRecipManager($recipeName = null)
    {
        return new RecipeManager($recipeName);
    }
}
