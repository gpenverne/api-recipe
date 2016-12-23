<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\StateManager;
use ApiRecipe\Manager\YmlParserTrait;
use ApiAi\Client;
use Symfony\Component\HttpFoundation\Request;

class VoiceController extends Controller
{
    use YmlParserTrait;

    /**
     * @var Client
     */
    protected $bot;

    /**
     * @return Client
     */
    protected function getBot()
    {
        $config = $this->loadConfig('bot');

        if (null === $this->bot) {
            $this->bot = new Client($config['bot']['access_token']);
        }

        return $this->bot;
    }

    /**
     * @return array
     */
    public function deduceAction()
    {
        $rawText = urldecode(strtolower($this->request->get('text')));
        $this->setResponseFormat('json');
        $this->getProvider('logger')->info($rawText);
        $texts = explode(',', $rawText);
        $voicesConfig = $this->getConfig('voices');
        $activated = false;
        /*
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
        */
        $recipes = $this->getRecipeManager()->getAll();
        foreach ($recipes as $recipeName => $recipe) {
            if (null === $recipe->voices) {
                return;
            }
            $recipeManager = $this->getRecipeManager($recipeName);
            foreach ($texts as $text) {
                if (false !== $state = $this->voiceMatch($text, $recipe->voices)) {
                    $recipe->url = sprintf('%s&state=%s', $recipe->url, $state);
                    $this->getProvider('logger')->info($recipeName);

                    $recipeManager->exec($state, $this->getProvider('logger'));

                    return [
                        'recipe' => $recipe,
                        'targetState' => $state,
                        'voiceMessage' => $recipeManager->getVoiceMessage($state),
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
}
