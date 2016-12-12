<?php

namespace spec\Homatisation\Provider;

use Homatisation\Provider\MilightProvider;
use PhpSpec\ObjectBehavior;
use Homatisation\Provider\ProviderInterface;

class BinaryProviderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([
            'binary' => '/a_sample/binary',
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MilightProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_returns_available_actions()
    {
        $this->getActions()->shouldBeArray();
    }
}
