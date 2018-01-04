<?php

namespace ApiRecipe\Controller;

class JarvisController extends Controller
{
    private $url;

    /**
     * @return array
     */
    public function indexAction()
    {
        $this->url = sprintf('%s:%s', $this->getConfig('server')['address'], $this->getConfig('server')['port']);
        $recipes = $this->getRecipeManager()->getAll();
        $responseContent = '';
        foreach ($recipes as $recipeName => $recipe) {
            if (!isset($recipe->voices)) {
                continue;
            }

            $responseContent = $this->appendRecipeToResponse($recipeName, $recipe, $responseContent);
        }

        die($responseContent);
    }

    private function appendRecipeToResponse($recipeName, $recipe, $responseContent)
    {
        foreach ($recipe->voices as $state => $triggers) {
            if (!isset($triggers['triggers'])) {
                continue;
            }
            foreach ($triggers['triggers'] as $trigger) {
                $trigger = str_replace(['%', '(.*?)'], ['', '*'], $trigger);
                $trigger = sprintf('%s=$(curl -s "http://%s/recipes/%s/exec?state=%s" > /dev/null)', $trigger, $this->url, $recipeName, $state);
                $responseContent .= $trigger."\n";
            }
        }

        return $responseContent;
    }
}
