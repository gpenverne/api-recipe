<?php

namespace ApiRecipe\Converter;

interface ConverterInterface
{
    /**
     * @param array $array
     *
     * @return \stdClass
     */
    public static function convert($array);
}
