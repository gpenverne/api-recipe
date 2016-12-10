<?php

namespace Homatisation\Converter;

use Doctrine\Common\Inflector\Inflector;

class ArrayToStdClassConverter implements ConverterInterface
{
    /**
     * @param array $array
     *
     * @return \stdClass
     */
    public static function convert($array)
    {
        $object = new \stdClass();

        foreach ($array as $k => $v) {
            $k = Inflector::Camelize($k);
            $object->$k = $v;
        }

        return $object;
    }
}
