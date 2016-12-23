<?php

namespace ApiRecipe\Provider;

use ApiRecipe\Manager\RequestManager;

class FreeboxProvider implements ProviderInterface, BotCompliantInterface
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
}
