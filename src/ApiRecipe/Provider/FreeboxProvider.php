<?php

namespace ApiRecipe\Provider;

use ApiRecipe\Manager\RequestManager;

class FreeboxProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $remoteCode;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'key',
            'turnOn',
            'turnOff',
        ];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function key($key)
    {
        $url = $this->getUrl($key);

        return $this->request($url);
    }

    /**
     * @return bool
     */
    public function turnOn()
    {
        if (!$this->lock()) {
            return false;
        }

        $this->key('power');

        return $this->lock();
    }

    /**
     * @return bool
     */
    public function turnOff()
    {
        if (!$this->unlock()) {
            return false;
        }

        $this->key('power');

        return $this->unlock();
    }

    public function handleParameters($parameters)
    {
        if (isset($parameters['number'])) {
            return $this->key($parameters['number']);
        }
    }
    /**
     * @param string $url
     *
     * @return bool
     */
    protected function request($url)
    {
        $requestManager = new RequestManager();

        return $requestManager->request($url);
    }
    /**
     * @param string $key
     *
     * @return string
     */
    protected function getUrl($key)
    {
        return sprintf('http://hd1.freebox.fr/pub/remote_control?code=%s&key=%s', $this->remoteCode, $key);
    }

    /**
     * @return bool
     */
    protected function lock()
    {
        $pidFile = '/tmp/freebox.pid';
        if (is_file($pidFile)) {
            return false;
        }

        touch($pidFile);

        return true;
    }

    /**
     * @return bool
     */
    protected function unlock()
    {
        $pidFile = '/tmp/freebox.pid';
        if (!is_file($pidFile)) {
            return false;
        }

        unlink($pidFile);

        return true;
    }
}
