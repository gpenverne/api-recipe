<?php

trait hydradtorTrait
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
            $this->$k = $v;
        }

        return $this;
    }
}
