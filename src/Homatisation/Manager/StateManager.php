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
    public function toggleRecipeState($recipeName)
    {
        $currentState = $this->getRecipeState($recipeName);

        if (self::STATE_ON === $currentState) {
            $newState = self::STATE_OFF;
        } else {
            $newState = self::STATE_ON;
        }

        $this->setRecipeState($recipeName, $newState);
    }

    /**
     * @param string $recipeName
     *
     * @return string
     */
    public function getRecipeState($recipeName)
    {
        $stateFile = $this->getRecipeStateFile($recipeName);
        if (!is_file($stateFile)) {
            $this->setRecipeState($recipeName, self::STATE_OFF);
        }

        return trim(file_get_contents($stateFile));
    }

    /**
     * @param string $recipeName
     * @param string $newState
     */
    public function setRecipeState($recipeName, $newState)
    {
        return file_put_contents($this->getRecipeStateFile($recipeName), $newState);
    }

    /**
     * @param string $recipeName
     *
     * @return string
     */
    private function getRecipeStateFile($recipeName)
    {
        return sprintf('%s/../../../var/states/%s.state', __DIR__, $recipeName);
    }
}
