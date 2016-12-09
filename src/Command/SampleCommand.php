<?php

namespace Command;

use Symfony\Component\Console\Command\Command;

class SampleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:sample-command')
            ->setDescription('Test the command composer')
            ->setHelp('This command allows you to create users...')
        ;
    }
}
