<?php

namespace spec\Homatisation\Converter;

use Homatisation\Converter\ArrayToStdClassConverter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayToStdClassConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayToStdClassConverter::class);
    }
}
