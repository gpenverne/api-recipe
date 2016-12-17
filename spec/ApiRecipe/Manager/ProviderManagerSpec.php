<?php

namespace spec\ApiRecipe\Manager;

use ApiRecipe\Manager\ProviderManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProviderManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProviderManager::class);
    }
}
