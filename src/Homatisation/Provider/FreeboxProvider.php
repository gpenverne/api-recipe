<?php

namespace Homatisation\Provider;

use Homatisation\Manager\RequestManager;

class FreeboxProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $remoteCode;

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
