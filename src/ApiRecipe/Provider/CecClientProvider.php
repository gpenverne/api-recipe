<?php

namespace ApiRecipe\Provider;

use Doctrine\Common\Inflector\Inflector;

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
            'turnOn',
            'turnOff',
            'volumeUp',
            'volumeDown',
            'setActiveSource',
        ];
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function command($command, $device = null)
    {
        $command = str_replace('/', ':', $command);
        if (null === $device) {
            $command = sprintf('echo %s | %s -s', $command, $this->binary);
            file_put_contents('/tmp/cec.log', $command);

            return shell_exec($command);
        }

        $device = $this->getDevice($device);

        return shell_exec(sprintf('echo %s %s | %s -s -d 1', $command, $device->address, $this->binary));
    }

    /**
     * @param string $deviceName
     *
     * @return bool
     */
    public function turnOn($deviceName = null)
    {
        return $this->command('on 0', $deviceName);
    }

    /**
     * @param string $deviceName
     *
     * @return bool
     */
    public function turnOff($deviceName = null)
    {
        return $this->command('standby 0', $deviceName);
    }

    /**
     * @param string $deviceName
     *
     * @return bool
     */
    public function volumeUp($deviceName = null)
    {
        return $this->command('volup');
    }

    /**
     * @param string $deviceName
     *
     * @return bool
     */
    public function volumeDown($deviceName = null)
    {
        return $this->command('voldown');
    }

    /**
     * @param string $deviceName
     *
     * @return bool
     */
    public function setActiveSource($deviceName = null)
    {
        if (null === $deviceName) {
            return $this->command('as');
        }

        return $this->command('spl', $deviceName);
    }

    /**
     * @param string $deviceName
     *
     * @return \stdClass
     */
    private function getDevice($deviceName)
    {
        $regexp = sprintf('/%s/', strtolower($deviceName));
        $devices = $this->getDevices();
        foreach ($devices as $device) {
            if (preg_match($regexp, $device->osdString)) {
                return $device;
            }
        }

        throw new \Exception(sprintf('Device %s not found', $deviceName));
    }

    /**
     * @return \stdClass[]
     */
    private function getDevices()
    {
        $returnDevices = [];
        $rawDevicesList = $this->command('scan');
        $devices = explode('device #', $rawDevicesList);
        unset($devices[0]);
        foreach ($devices as $device) {
            $class = new \stdClass();
            $lines = explode("\n", $device);
            array_pop($lines);
            array_shift($lines);
            foreach ($lines as $line) {
                $values = array_values(explode(':', $line));
                if (!isset($values[1]) || $values[0] == '') {
                    continue;
                }

                list($key, $value) = $values;
                $key = Inflector::camelize($key);
                $class->$key = strtolower(trim($value));
            }
            $returnDevices[] = $class;
        }

        return $returnDevices;
    }
}
