<?php

namespace Homatisation\Command;

use Symfony\Component\Console\Command\Command;

class SampleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('homatisation:sample-command')
            ->setDescription('Test the command composer')
            ->setHelp('This command allows you to create users...')
        ;
    }
}
