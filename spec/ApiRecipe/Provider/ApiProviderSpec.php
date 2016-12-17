<?php

namespace spec\ApiRecipe\Provider;

use ApiRecipe\Provider\ApiProvider;
use PhpSpec\ObjectBehavior;
use ApiRecipe\Provider\ProviderInterface;

class ApiProviderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([
            'baseUrl' => 'http://an_host',
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiProvider::class);
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
