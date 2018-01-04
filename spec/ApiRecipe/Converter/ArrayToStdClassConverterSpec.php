<?php

namespace spec\ApiRecipe\Converter;

use ApiRecipe\Converter\ArrayToStdClassConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayToStdClassConverterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ArrayToStdClassConverter::class);
    }

    public function it_converts_array_to_stdClass()
    {
        $array = ['foo' => 'bar'];

        $this->convert($array)->shouldHaveType(\stdClass::class);
    }
}
