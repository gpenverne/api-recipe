<?php

namespace spec\Homatisation\Provider;

use Homatisation\Provider\LoggerProvider;
use PhpSpec\ObjectBehavior;
use Homatisation\Provider\ProviderInterface;

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
