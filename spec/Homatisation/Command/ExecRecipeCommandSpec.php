<?php

namespace spec\Homatisation\Command;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Command\Command;

class ExecRecipeCommandSpec extends ObjectBehavior
{
    public function it_is_a_command()
    {
        $this->shouldBeAnInstanceOf(Command::class);
    }
}
