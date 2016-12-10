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
     * @param string $recipeName
     */
    public function __construct($recipeName)
    {
        $this->recipeName = $recipeName;
        $this->stateManager = new StateManager();
        $this->loadConfig();
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
            if ($this->stateManager->isOn($this->recipeName)) {
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

        return $result;
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
     * @return $this
     */
    protected function loadConfig()
    {
        $expectedFile = sprintf('%s/../../../recipes/%s.yml', __DIR__, $this->recipeName);

        $infos = array_merge($this->infos, $this->parseYmlFile($expectedFile));
        $this->infos = ArrayToStdClassConverter::convert($infos);

        if (!isset($this->infos->actions[StateManager::STATE_ON])) {
            $this->infos->actions[StateManager::STATE_ON] = [];
        }
        if (!isset($this->infos->actions[StateManager::STATE_OFF])) {
            $this->infos->actions[StateManager::STATE_OFF] = [];
        }
        if (!isset($this->infos->actions[StateManager::STATE_EACH_TIME])) {
            $this->infos->actions[StateManager::STATE_EACH_TIME] = [];
        }

        return $this;
    }
}
