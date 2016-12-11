<?php

namespace Homatisation\Provider;

class SleepProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'sleep',
        ];
    }

    /**
     * @param int $key
     *
     * @return bool
     */
    public function sleep($seconds)
    {
        sleep((int) $seconds);

        return true;
    }
}
