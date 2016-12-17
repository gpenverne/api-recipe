<?php

namespace spec\ApiRecipe\Provider;

use ApiRecipe\Provider\LoggerProvider;
use PhpSpec\ObjectBehavior;
use ApiRecipe\Provider\ProviderInterface;

class LoggerProviderSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith([
            'logFile' => '/a_sample/log/file/path',
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LoggerProvider::class);
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
