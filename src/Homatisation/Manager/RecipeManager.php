<?php

namespace Homatisation\Manager;

use Homatisation\Converter\ArrayToStdClassConverter;

class RecipeManager implements ManagerInterface
{
    use YmlParserTrait;

    /**
     * @var array
     */
    private $infos = [
        'title' => null,
        'description' => null,
        'picture' => null,
        'actions' => [
            StateManager::STATE_ON => [],
            StateManager::STATE_OFF => [],
            StateManager::STATE_EACH_TIME => [],
        ],
    ];

    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * @param null|string $recipeName
     */
    public function __construct($recipeName = null)
    {
        if (null !== $recipeName) {
            $this->recipeName = $recipeName;
            $this->infos = $this->loadConfig($recipeName);
        }
    }

    /**
     * @return array
     */
    public function list($recipeName = null)
    {
        $recipes = [];
        $recipesFolder = sprintf('%s/../../../recipes', __DIR__);
        $cursor = opendir($recipesFolder);

        while ($f = readdir($cursor)) {
            if ('.' !== $f && '..' != $f) {
                $recipe = str_replace('.yml', '', $f);
                $recipeInfos = $this->loadConfig($recipe);

                if ($recipe === $recipeName) {
                    return $recipeInfos;
                }

                $recipes[] = $recipeInfos;
            }
        }

        return $recipes;
    }

    /**
     * @param string $state
     *
     * @return array
     */
    public function exec($state = null)
    {
        $result = [];

        if ($state == null) {
            if ($this->getStateManager()->isOn($this->recipeName)) {
                $state = StateManager::STATE_ON;
            } else {
                $state = StateManager::STATE_OFF;
            }
        }

        $actions = $this->getActions($state);

        foreach ($actions as $action) {
            $actionParameters = explode(':', $action);
            if (2 === count($actionParameters)) {
                list($provider, $method) = $actionParameters;
                $result[$action] = $this->execAction($provider, $method);
            } else {
                list($provider, $method, $argument) = $actionParameters;
                $result[$action] = $this->execAction($provider, $method, $argument);
            }
        }

        $this->getStateManager()->toggleRecipeState($this->recipeName);

        return [
            'actions' => $result,
        ];
    }

    /**
     * @param string $provider
     * @param string $method
     * @param string $argument
     *
     * @return bool
     */
    protected function execAction($provider, $method, $argument = null)
    {
        $action = new ActionManager($provider, $method, $argument);

        return $action->exec();
    }

    /**
     * @param string $state
     *
     * @return array
     */
    protected function getActions($state = null)
    {
        $actions = $this->infos->actions[StateManager::STATE_EACH_TIME];

        if (StateManager::STATE_ON === $state) {
            $actions = array_merge($actions, $this->infos->actions[StateManager::STATE_ON]);
        } elseif (StateManager::STATE_OFF === $state) {
            $actions = array_merge($actions, $this->infos->actions[StateManager::STATE_OFF]);
        }

        return $actions;
    }

    /**
     * @param string $recipeName
     *
     * @return $this
     */
    protected function loadConfig($recipeName)
    {
        $expectedFile = sprintf('%s/../../../recipes/%s.yml', __DIR__, $recipeName);

        $infos = array_merge($this->infos, $this->parseYmlFile($expectedFile));
        $infos = ArrayToStdClassConverter::convert($infos);

        if (!isset($infos->actions[StateManager::STATE_ON])) {
            $infos->actions[StateManager::STATE_ON] = [];
        }
        if (!isset($infos->actions[StateManager::STATE_OFF])) {
            $infos->actions[StateManager::STATE_OFF] = [];
        }
        if (!isset($infos->actions[StateManager::STATE_EACH_TIME])) {
            $infos->actions[StateManager::STATE_EACH_TIME] = [];
        }

        $infos->state = $this->getStateManager()->getRecipeState($recipeName);
        $infos->url = sprintf('/recipes/exec/%s?format=json', $recipeName);
        $infos->visible = true;

        return $infos;
    }

    /**
     * @return StateManager
     */
    protected function getStateManager()
    {
        if (null === $this->stateManager) {
            $this->stateManager = new StateManager();
        }

        return $this->stateManager;
    }
}
