<?php

namespace Homatisation\Provider;

use Homatisation\Lib\Milight;

class MilightProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var Milight;
     */
    protected $milight;

    /**
     * @param string $method
     * @param array  $args
     *
     * @return bool
     */
    public function __call($method, $args)
    {
        $milight = $this->getMilight();

        return (bool) call_user_func_array([
            $milight,
            $method,
        ], $args);
    }

    /**
     * @return MiLight
     */
    private function getMilight()
    {
        if (null !== $this->milight) {
            return $this->milight;
        }

        $this->milight = new MiLight($this->host);

        return $this->milight;
    }
}
