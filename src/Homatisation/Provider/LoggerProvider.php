<?php

namespace Homatisation\Provider;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerProvider implements ProviderInterface
{
    const NAME = 'logger_provider';

    use HydratorTrait;

    /**
     * @var Logger;
     */
    protected $logger;

    /**
     * @var StreamHandler;
     */
    protected $streamHandler;

    /**
     * @var string
     */
    protected $logFile;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'debug',
            'error',
            'info',
            'warning',
        ];
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function debug($string)
    {
        return (bool) $this->getLogger()->debug($string);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function error($string)
    {
        return (bool) $this->getLogger()->error($string);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function info($string)
    {
        return (bool) $this->getLogger()->info($string);
    }

    /**
     * @param string $string
     *
     * @return bool
     */
    public function warning($string)
    {
        return (bool) $this->getLogger()->warning($string);
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        if (null !== $this->logger) {
            return $this->logger;
        }

        $this->logger = new Logger(self::NAME);
        $this->logger->pushHandler($this->getStreamHandler());

        return $this->logger;
    }

    /**
     * @return StreamHandler
     */
    protected function getStreamHandler()
    {
        if (null !== $this->streamHandler) {
            return $this->streamHandler;
        }

        $logFile = sprintf('%s/../../../%s', __DIR__, $this->logFile);
        $this->streamHandler = new StreamHandler($logFile, Logger::DEBUG);

        return $this->streamHandler;
    }
}
