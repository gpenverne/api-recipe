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

    public function actionOnRGBW($string)
    {
        // milight:actionOnRGBW:1|setAllOn|optionalParams

        $params = explode('|', $string);
        $milight = $this->getMilight();
        $milight->setRgbwActiveGroup($params[0]);

        $method = $params[1];
        if (isset($params[2])) {
            return $milight->$method($params[2]);
        }

        return $milight->$method();
    }

    public function actionOnWhite($string)
    {
        // milight:actionOnWhite:1|setAllOn|optionalParams

        $params = explode('|', $string);
        $milight = $this->getMilight();
        $milight->setWhiteActiveGroup($params[0]);

        $method = $params[1];
        if (isset($params[2])) {
            return $milight->$method($params[2]);
        }

        return $milight->$method();
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
