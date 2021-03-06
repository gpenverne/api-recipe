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
        $rawText = urldecode(strtolower($this->get('request')->get('text')));
        $this->setResponseFormat('json');
        $this->getProvider('logger')->info($rawText);
        $texts = explode(',', $rawText);
        $voicesConfig = $this->getConfig('voices');
        $activated = false;
        $return = [
            'command' => $rawText,
            'recipe' => null,
            'targetState' => null,
        ];

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

                    $return = [
                        'recipe' => $recipe,
                        'targetState' => $state,
                        'voiceMessage' => $recipeManager->getVoiceMessage($state),
                    ];
                }
            }
        }

        return $return;
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
        if (isset($_GET['state'])) {
            return $_GET['state'];
        }
        foreach ($states as $state) {
            if (isset($recipeVoice[$state]) && isset($recipeVoice[$state]['triggers'])) {
                foreach ($recipeVoice[$state]['triggers'] as $trigger) {
                    if (preg_match($trigger, $text)) {
                        return $state;
                    }
                }
            }
        }

        return false;
    }
}
