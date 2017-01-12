<?php

namespace ApiRecipe\Provider;

class BroadlinkProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $hostIp;

    /**
     * @var string
     */
    protected $hostPort;

    /**
     * @var string
     */
    protected $hostMac;

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
     * @param  string $commandName
     *
     * @return bool
     */
    public function command($commandName)
    {
        $binary = $this->getGatewayBinary();
        $shell_command = sprintf('%s %s %s %s %s', $binary, $this->hostIp, $this->hostPort, $this->hostMac, $commandName);

        return (bool) exec($shell_command);
    }

    /**
     * @return string
     */
    protected function getGatewayBinary()
    {
        return sprintf('%s/../../../gateway/Broadlink/broadlink.py', __DIR__);
    }
}
