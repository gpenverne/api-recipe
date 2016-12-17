<?php

namespace spec\ApiRecipe\Provider;

use ApiRecipe\Provider\AndroidProvider;
use PhpSpec\ObjectBehavior;
use ApiRecipe\Provider\ProviderInterface;

class AndroidProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(AndroidProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_returns_available_actions()
    {
        $this->getActions()->shouldBeArray();
    }

    public function it_returns_an_app_package_name()
    {
        $this->openApp('a_package_name')->shouldReturn('a_package_name');
    }
}
