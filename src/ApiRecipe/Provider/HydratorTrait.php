<?php

namespace ApiRecipe\Provider;

use Doctrine\Common\Inflector\Inflector;

trait HydratorTrait
{
    protected $providerName;

    /**
      * @param array $properties
      */
    public function __construct($params = [], $providerName = null)
    {
        $this->hydrate($params);
        $this->providerName = $providerName;
    }

    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return $this
     */
    protected function hydrate($properties = [])
    {
        foreach ($properties as $k => $v) {
            $k = Inflector::Camelize($k);
            $this->$k = $v;
        }

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    protected function set($key, $value)
    {
        $this->$key = $value;

        return $this;
    }
}
