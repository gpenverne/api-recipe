<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\StateManager;
use ApiRecipe\Manager\YmlParserTrait;

class VoiceController extends Controller
{
    use YmlParserTrait;

    /**
     * @return array
     */
    public function deduceAction()
    {
        $rawText = urldecode(strtolower($this->request->get('text')));
        $this->setResponseFormat('json');
        $this->getProvider('logger')->info($rawText);
        $texts = explode(',', $rawText);
        $voicesConfig = $this->getVoicesConfig();
        $activated = false;

        if (isset($voicesConfig['keywords'])) {
            $clearedTexts = [];
            foreach ($voicesConfig['keywords'] as $keyword) {
                $keyword = strtolower($keyword);
                foreach ($texts as $text) {
                    $text = strtolower($text);
                    if (false !== strpos($text, $keyword)) {
                        $activated = true;
                    }
                    $clearedTexts[] = trim(str_replace(trim($keyword), '', $text));
                }
                $texts = $clearedTexts;
            }
            if (false === $activated) {
                return [
                    'recipe' => null,
                    'targetState' => null,
                ];
            }
        }

        $recipes = $this->getRecipeManager()->getAll();
        foreach ($recipes as $recipe) {
            if (null === $recipe->voices) {
                return;
            }
            foreach ($texts as $text) {
                if (false !== $state = $this->voiceMatch($text, $recipe->voices)) {
                    $recipe->url = sprintf('%s&state=%s', $recipe->url, $state);
                    $fullUrl = sprintf('http://%s%s', $_SERVER['SERVER_NAME'], $recipe->url);
                    file_get_contents($fullUrl);
                    $this->getProvider('logger')->info($fullUrl);

                    //$this->getRecipeManager($recipe->title)->exec($state, $this->getProvider('logger'));

                    return [
                        'recipe' => null,
                        'targetState' => null,
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
        $text = trim(strtolower($text));
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

    protected function getVoicesConfig()
    {
        $file = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($file);
        if (!isset($config['voices'])) {
            return [];
        }

        return $config['voices'];
    }
}
