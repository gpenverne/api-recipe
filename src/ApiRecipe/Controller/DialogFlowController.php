<?php

namespace ApiRecipe\Controller;

use ApiRecipe\Manager\StateManager;

class DialogFlowController extends Controller
{
    /**
     * @return string
     */
    public function webhookAction()
    {
        $this->setResponseFormat('json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (is_array($data)) {
            $data = @json_decode(array_keys($data)[0], true);
        }
        if (null === $data) {
            $data = ['result' => ['resolvedQuery' => (isset($_GET['text']) ? $_GET['text'] : ''), 'parameters' => ['recipe' => (isset($_GET['recipe']) ? $_GET['recipe'] : 'tell'), 'state' => (isset($_GET['state']) ? $_GET['state'] : 'tell')]]];
        }
        if (isset($data['queryResult'])) {
            $data = ['result' => [
                'resolvedQuery' => $data['queryResult']['queryText'],
                'parameters' => [
                    'result' => $data['queryResult']['parameters']['recipe'],
                    'state' => $data['queryResult']['parameters']['state'],
                ],
            ]];
        }
        $recipe = $data['result']['parameters']['recipe'];
        if (is_array($recipe)) {
            $recipe = $recipe[0];
        }
        $state = $data['result']['parameters']['state'];
        $speech = 'C\'est fait';
        if ($recipe === 'tell') {
            $speech = trim(str_replace(['dit ', 'tell ', 'dire ', '"', 'Dit', 'Tell', 'Dire'], '', $data['result']['resolvedQuery']));
            $result['speech'] = $speech;
        } else {
            try {
                $recipename = $recipe;
                $recipe = $this->getRecipeManager($recipe);
                $result = $recipe->exec($state);
            } catch (\Exception $e) {
                $result = $this->deduce($data['result']['resolvedQuery']);
            }

            if (null === $result) {
                $result = $this->deduce($data['result']['resolvedQuery']);
            }
        }
        if (isset($result['speech']) && null !== $result['speech']) {
            $speech = $result['speech'];
            if (isset($speech[0]) && "1" === $speech[0]) {
                $speech = substr($speech, 1);
            }
        }

        if ($speech) {
            file_put_contents('/tmp/to-tell', $speech);
        }

        $result = [
            'speech' => null,
            'displayText' => $speech,
            'data' => $result,
            'contextOut' => [],
            'source' => 'homatisation',
        ];

        return $result;
    }

    private function deduce($rawText)
    {
        $texts = explode(',', $rawText);
        $voicesConfig = $this->getConfig('voices');
        $activated = false;
        file_put_contents('/tmp/last-deduced', $rawText);
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

                    $result = $recipeManager->exec($state, $this->getProvider('logger'));
                    if (isset($result['speech'])) {
                        $speech = $result['speech'];
                    } else {
                        $speech = null;
                    }
                    $return = [
                        'recipe' => $recipe,
                        'targetState' => $state,
                        'voiceMessage' => $recipeManager->getVoiceMessage($state),
                        'result' => $result,
                        'speech' => $speech,
                        'fulfillmentText' => $speech,
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
