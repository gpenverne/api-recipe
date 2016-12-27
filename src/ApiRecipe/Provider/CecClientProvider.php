<?php

namespace ApiRecipe\Provider;

class CecClientProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $binary = '/usr/bin/cec-client';

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'command',
        ];
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function command($command)
    {
        return shell_exec(sprintf('echo %s | %s -s -d 1', $command, $this->binary));
    }
}
