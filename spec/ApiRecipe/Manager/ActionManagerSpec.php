<?php

namespace spec\ApiRecipe\Manager;

use ApiRecipe\Manager\ActionManager;
use PhpSpec\ObjectBehavior;

class ActionManagerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('a_provider', 'a_method', 'an_argument');
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType(ActionManager::class);
    }
}
