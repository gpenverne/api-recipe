<?php

namespace spec\Homatisation\Manager;

use Homatisation\Manager\StateManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StateManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(StateManager::class);
    }
}
