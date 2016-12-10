<?php

namespace Homatisation\Provider;

class SleepProvider implements ProviderInterface
{
    use HydratorTrait;

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
