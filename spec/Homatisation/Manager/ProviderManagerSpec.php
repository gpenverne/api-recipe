<?php

namespace spec\Homatisation\Manager;

use Homatisation\Manager\ProviderManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProviderManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProviderManager::class);
    }
}
