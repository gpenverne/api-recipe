<?php

namespace Homatisation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CreateConfigFileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('config:file:create')
            ->setDescription('Copy the config.yml.dist file to config.yml')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null)
    {
        return self::createConfigFile();
    }

    public static function createConfigFile($event = null)
    {
        $distFile = sprintf('%s/../../../app/config/config.yml.dist', __DIR__);
        $localFile = sprintf('%s/../../../app/config/config.yml', __DIR__);

        copy($distFile, $localFile);
    }
}
