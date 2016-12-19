<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\StateManager;

class VoiceController extends Controller
{
    /**
     * @return array
     */
    public function deduceAction()
    {
        $this->setResponseFormat('json');
        file_put_contents('/tmp/log', $this->request->get('text'));
        $texts = explode(',', $this->request->get('text'));

        $recipes = $this->getRecipeManager()->getAll();
        foreach ($recipes as $recipe) {
            if (null === $recipe->voices) {
                return;
            }
            foreach ($texts as $text) {
                if (false !== $state = $this->voiceMatch($text, $recipe->voices)) {
                    $recipe->url = sprintf('%s&state=%s', $recipe->url, $state);

                    return [
                    'recipe' => $recipe,
                    'targetState' => $state,
                ];
                }
            }
        }

        return [
            'recipe' => null,
            'targetState' => null,
        ];
    }

    /**
     * @param string    $text
     * @param \stdClass $recipeVoice
     *
     * @return false|string
     */
    protected function voiceMatch($text, $recipeVoice)
    {
        $text = strtolower($text);

        $texts = [];
        $states = [
            StateManager::STATE_ON,
            StateManager::STATE_OFF,
            StateManager::STATE_EACH_TIME,
        ];

        foreach ($states as $state) {
            if (isset($recipeVoice[$state]) && isset($recipeVoice[$state]['triggers'])) {
                foreach ($recipeVoice[$state]['triggers'] as $trigger) {
                    $texts[strtolower($trigger)] = $state;
                }
            }
        }

        if (isset($texts[$text])) {
            return $texts[$text];
        }

        return false;
    }
}
