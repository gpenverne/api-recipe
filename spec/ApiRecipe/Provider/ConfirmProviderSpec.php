<?php

namespace spec\ApiRecipe\Provider;

use ApiRecipe\Provider\ConfirmProvider;
use ApiRecipe\Provider\ProviderInterface;
use PhpSpec\ObjectBehavior;

class ConfirmProviderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfirmProvider::class);
    }

    public function it_is_a_provider()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_returns_an_array_of_actions()
    {
        $this->getActions()->shouldReturn([
            'confirm',
        ]);
    }

    public function it_returns_a_confirm_message()
    {
        $this->confirm('something')->shouldReturn('something');
    }
}
