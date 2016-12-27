<?php

namespace ApiRecipe\Command;

use ApiRecipe\Manager\YmlParserTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebServerCommand extends Command
{
    use YmlParserTrait;

    protected function configure()
    {
        $this
            ->setName('ApiRecipe:server')
            ->setDescription('start the web server')
            ->addArgument('action', InputArgument::OPTIONAL, 'start|stop|restart', 'start')
            ->addArgument('address', InputArgument::OPTIONAL, 'the server\'s address')
            ->addArgument('port', InputArgument::OPTIONAL, 'the server\'s port')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $address = $input->getArgument('address');
        $port = $input->getArgument('port');

        if (null === $address || null === $action) {
            $configFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
            $config = $this->parseYmlFile($configFile);
            if (!isset($config['server'])) {
                throw new \Exception('Please specify server port and address (at least in config.yml)');
            }

            $serverConfig = $config['server'];
            if (null === $address) {
                if (!isset($serverConfig['address'])) {
                    throw new \Exception('Please specify server address (at least in config.yml)');
                }
                $address = $serverConfig['address'];
            }
            if (null === $port) {
                if (!isset($serverConfig['port'])) {
                    throw new \Exception('Please specify server port (at least in config.yml)');
                }
                $port = $serverConfig['port'];
            }
        }

        switch ($action) {
            case 'start':
                return $this->startWebServer($address, $port);
                break;
            case 'stop':
                return $this->stopWebServer($address, $port);
                break;
            case 'restart':
                $this->stopWebServer($address, $port);

                return $this->startWebServer($address, $port);
                break;
        }
    }

    /**
     * @param string $address
     * @param int    $port
     */
    protected function startWebServer($address, $port)
    {
        $logFile = sprintf('%s/../../../var/logs/webserver.log', __DIR__);
        $cmd = sprintf('php -S %s:%s -t %s/../../../web/ > %s & echo $!', $address, $port, __DIR__, $logFile);
        $pid = exec($cmd, $output);
        $this->savePid($pid);
    }

    /**
     * @param string $address
     * @param int    $port
     */
    protected function stopWebServer($address, $port)
    {
        $pid = $this->getPid();

        $cmd = sprintf('kill -9 %s', $pid);
        shell_exec($cmd);
    }

    /**
     * @param int $pid
     *
     * @return bool
     */
    private function savePid($pid)
    {
        $pidFile = '/tmp/ApiRecipe.pid';

        return file_put_contents($pidFile, $pid);
    }

    /**
     * @return int
     */
    private function getPid()
    {
        $pidFile = '/tmp/ApiRecipe.pid';
        if (!is_file($pidFile)) {
            return null;
        }

        return trim((int) file_get_contents($pidFile));
    }
}
