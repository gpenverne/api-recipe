<?php

namespace Homatisation\Manager;

use Doctrine\Common\Inflector\Inflector;
use Homatisation\Provider\ProviderInterface;

class ProviderManager implements ManagerInterface
{
    use YmlParserTrait;

    /**
     * @param string $providerName
     *
     * @return ProviderInterface
     */
    public function getProvider($providerName)
    {
        $providerName = Inflector::Camelize($providerName);
        $config = $this->getConfig($providerName);
        if (empty($config)) {
            throw new \Exception(sprintf('Provider %s not found.', $providerName));
        }
        $providerName = $config['provider'];
        $expectedClass = sprintf('Homatisation\\Provider\\%sProvider', ucfirst($providerName));

        return new $expectedClass($config);
    }

    /**
     * @return $this
     */
    protected function getConfig($providerName)
    {
        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);

        foreach ($config['providers'] as $key => $providerConfig) {
            if (Inflector::Camelize($key) === $providerName) {
                return $providerConfig;
            }
        }

        return [];
    }
}
