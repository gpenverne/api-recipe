<?php

namespace ApiRecipe\Provider;

use ApiRecipe\Lib\Milight;

class MilightProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var Milight;
     */
    protected $milight;

    /**
     * @return array
     */
    public function getActions()
    {
        $reflectedClass = new \ReflectionClass(Milight::class);

        $methods = $reflectedClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        return array_map(function ($reflectionMethod) {
            return $reflectionMethod->name;
        }, $methods);
    }

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

    public function rgbwDiscoSlowerMode($group = 1)
    {
        $this->getMilight()->setRgbwActiveGroup($group);
        $this->getMilight()->rgbwDiscoSlowerMode();

        return $this;
    }

    public function rgbwBrightnessPercent($brightnessPercent, $group = null)
    {
        return $this->getMilight()->rgbwBrightnessPercent($brightnessPercent, $group);
    }

    /**
     * @param int $group
     *
     * @return $this
     */
    public function rgbwSetColorToOrange($group = null)
    {
        if (null !== $group) {
            $this->getMilight()->setRgbwActiveGroup($group);
        }

        $this->getMilight()->rgbwSetColorToOrange();

        return $this;
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
