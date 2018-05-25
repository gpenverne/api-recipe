<?php

namespace ApiRecipe\Provider;

class PingerProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'wait',
            'need',
            'lock',
        ];
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    public function wait($ip)
    {
        exec('ping -c1 '.$ip, $output, $return_var);
        while ($return_var !== 0) {
            exec('ping -c1 '.$ip, $output, $return_var);
            sleep(1);
        }
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    public function need($ip)
    {
        $i = 0;
        $return_var = 1;
        while ($return_var !== 0) {
            exec('ping -i 0.2 -c1 '.$ip, $output, $return_var);
            if ($i && $return_var !== 0) {
                die('Ping not ready but needed');
            }
            ++$i;
        }
    }

    public function lock($ip)
    {
        $i = 0;
        echo 'pinging!';
        exec('ping -i 0.2 -c1 '.$ip, $output, $return_var);
        var_dump($return_var);
        if ($return_var !== 1) {
            die('Ping  ready but not needed');
        }
    }
}
