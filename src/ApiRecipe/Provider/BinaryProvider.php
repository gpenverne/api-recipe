<?php

namespace ApiRecipe\Provider;

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
            'escapeCommand',
            'echoUsingCommand',
        ];
    }

    public function escapeCommand($command = null)
    {
        if (null === $command || "null" === $command) {
            $command = $_GET['args'];
        }
        return $this->command('"'.$command.'"');
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
        file_put_contents('/dev/shm/binary-input', $fullCommand);
        $lastResult = exec($fullCommand);

        file_put_contents('/dev/shm/binary-output', $lastResult);
        return (bool) $lastResult;
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
