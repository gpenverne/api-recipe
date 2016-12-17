<?php

namespace spec\ApiRecipe\Converter;

use ApiRecipe\Converter\ArrayToStdClassConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayToStdClassConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayToStdClassConverter::class);
    }
}
