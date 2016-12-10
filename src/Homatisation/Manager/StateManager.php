<?php

namespace Homatisation\Manager;

class StateManager implements ManagerInterface
{
    const STATE_ON = 'on';

    const STATE_OFF = 'off';

    const STATE_EACH_TIME = 'each_time';

    /**
     * @param string $recipeName
     *
     * @return bool
     */
    public function isOn($recipeName)
    {
        return self::STATE_ON === $this->getRecipeState($recipeName);
    }

    /**
     * @param string $recipeName
     *
     * @return bool
     */
    public function isOff($recipeName)
    {
        return self::STATE_OFF === $this->getRecipeState($recipeName);
    }

    /**
     * @param string $recipeName
     *
     * @return string
     */
    protected function getRecipeState($recipeName)
    {
        $stateFile = sprintf('%s/../../var/states/%s.state', __DIR__, $recipeName);
        if (!is_file($stateFile)) {
            file_put_contents($stateFile, self::STATE_OFF);
        }

        return trim(file_get_contents($stateFile));
    }
}
