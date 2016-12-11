<?php

namespace Homatisation\Provider;

class AndroidProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'openApp',
        ];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function openApp($appName)
    {
        return $appName;
    }
}
