<?php

namespace spec\Homatisation\Provider;

use Homatisation\Provider\MilightProvider;
use PhpSpec\ObjectBehavior;
use Homatisation\Provider\ProviderInterface;

class MilightProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(MilightProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }
}
