<?php

namespace spec\Homatisation\Command;

use Homatisation\Command\SampleCommand;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Command\Command;

class SampleCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(SampleCommand::class);
    }

    public function it_is_a_command()
    {
        $this->shouldBeAnInstanceOf(Command::class);
    }
}
