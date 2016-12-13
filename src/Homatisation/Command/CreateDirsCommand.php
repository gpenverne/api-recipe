<?php

namespace Homatisation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CreateDirsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dirs:create')
            ->setDescription('Create required dirs')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input = null, OutputInterface $output = null)
    {
        return self::createDirs();
    }

    public static function createDirs($event = null)
    {
        $dirs = [
            'var',
            'var/states',
            'var/logs',
            'var/cache',
            'web/images',
        ];

        foreach ($dirs as $dir) {
            $targetDir = sprintf('%s/../../../%s', __DIR__, $dir);
            if (!is_dir($targetDir)) {
                mkdir($targetDir);
            }
        }
    }
}
