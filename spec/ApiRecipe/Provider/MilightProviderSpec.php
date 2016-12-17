<?php

namespace spec\ApiRecipe\Provider;

use ApiRecipe\Provider\MilightProvider;
use PhpSpec\ObjectBehavior;
use ApiRecipe\Provider\ProviderInterface;
use ApiRecipe\Lib\Milight;

class MilightProviderSpec extends ObjectBehavior
{
    public function let(Milight $milight)
    {
        $this->beConstructedWith([
            'milight' => $milight,
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

    public function it_calls_milight_library($milight)
    {
        $milight->rgbwAllOn()->shouldBeCalled();
        $this->rgbwAllOn();
    }
}
