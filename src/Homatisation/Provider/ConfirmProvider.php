<?php

namespace Homatisation\Provider;

class ConfirmProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'confirm',
        ];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function confirm($message)
    {
        return $message;
    }
}
