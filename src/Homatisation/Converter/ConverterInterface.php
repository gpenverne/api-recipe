<?php

namespace Homatisation\Converter;

interface ConverterInterface
{
    /**
     * @param array $array
     *
     * @return \stdClass
     */
    public static function convert($array);
}
