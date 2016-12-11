<?php

namespace Homatisation\Provider;

class BinaryProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $binary;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'command',
            'echoUsingCommand',
        ];
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function command($command = null)
    {
        if (null !== $command) {
            $fullCommand = sprintf('%s %s', $this->binary, $command);
        } else {
            $fullCommand = $this->binary;
        }

        return (bool) shell_exec($fullCommand);
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function echoUsingCommand($command)
    {
        $fullCommand = sprintf('echo "%s" | %s', $command, $this->binary);

        return (bool) shell_exec($fullCommand);
    }
}
