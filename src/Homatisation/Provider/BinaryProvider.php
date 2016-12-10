<?php

namespace Homatisation\Provider;

class CecClientProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $binary;

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
    public function echo($command)
    {
        $fullCommand = sprintf('echo "%s" | %s', $command, $this->binary);

        return (bool) shell_exec($fullCommand);
    }
}
