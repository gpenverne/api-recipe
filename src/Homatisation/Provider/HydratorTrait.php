<?php

namespace Homatisation\Provider;

use Doctrine\Common\Inflector\Inflector;

trait HydratorTrait
{
    /**
      * @param array $properties
      */
     public function __construct($params = [])
     {
         $this->hydrate($params);
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
}
