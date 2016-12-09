<?php

namespace Homatisation\Provider;

use Homatisation\Lib\Milight;

class MilightProvider implements ProviderInterface
{
    use hydratorTrait;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'turn-light-on' => 'rgbwAllOn',
        ];
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $host;
    }

    private function getMilight()
    {
        if (null !== $this->milight) {
            return $this->miLight;
        }

        $this->milight = new MiLight($this->host);

        return $this->milight;
    }

    /**
     * @return bool
     */
    private function rgbwAllOn()
    {
        return $this->getMilight()->rgbwAllOn();
    }
}
