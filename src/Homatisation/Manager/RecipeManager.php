<?php

namespace Homatisation\Manager;

class RecipeManager
{
    const STATE_ON = 'on';

    const STATE_OFF = 'off';

    use YmlParserTrait;

    /**
     * @var array
     */
    private $infos = [];

    /**
     * @param string $recipeName
     */
    public function __construct($recipeName)
    {
        $this->recipeName = $recipeName;
        $this->loadConfig();
    }

    public function exec($state = null)
    {
        $actions = $this->getActions($state);
        foreach ($actions as $action) {
            list($provider, $method, $argument) = $action;
            $this->execAction($provider, $method, $argument);
        }
    }

    /**
     * @param string $provider
     * @param string $method
     * @param string $argument
     *
     * @return bool
     */
    protected function execAction($provider, $method, $argument)
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
        if (null === $state) {
            $actions = $this->actions['each_time'];
        } elseif (self::STATE_ON === $state) {
            $actions = $this->actions['on'];
        } elseif (self::STATE_OFF === $state) {
            $actions = $this->actions['off'];
        }

        return $actions;
    }

    /**
     * @return $this
     */
    protected function loadConfig()
    {
        $expectedFile = sprintf('%s/../../recipes/%s.yml', __DIR__, $this->recipeName);

        $this->infos = $this->parseYmlFile($expectedFile);

        return $this;
    }
}
