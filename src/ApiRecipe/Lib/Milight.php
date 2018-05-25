<?php
/*
 * Milight/LimitlessLED/EasyBulb PHP API
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Yashar Rashedi <info@rashedi.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/

namespace ApiRecipe\Lib;

class Milight
{
    private $host;
    private $port;
    private $delay = 10000; //microseconds
    private $command_repeats = 10;
    private $rgbwActiveGroup = 0; // 0 means all
    private $whiteActiveGroup = 0; // 0 means all
    private $commandCodes = [
        //RGBW Bulb commands
        'rgbwAllOn' => [0x42, 0x00],
        'rgbwAllOff' => [0x41, 0x00],
    'rgbwGroup0Off' => [0x41, 0x00],
    'rgbwGroup0On' => [0x42, 0x00],
        'rgbwGroup1On' => [0x45, 0x00],
        'rgbwGroup2On' => [0x47, 0x00],
        'rgbwGroup3On' => [0x49, 0x00],
        'rgbwGroup4On' => [0x4B, 0x00],
        'rgbwGroup1Off' => [0x46, 0x00],
        'rgbwGroup2Off' => [0x48, 0x00],
        'rgbwGroup3Off' => [0x4a, 0x00],
        'rgbwGroup4Off' => [0x4c, 0x00],
        'rgbwAllNightMode' => [0xC1, 0x00],
        'rgbwGroup0NightMode' => [0xC1, 0x00],
        'rgbwGroup1NightMode' => [0xC6, 0x00],
        'rgbwGroup2NightMode' => [0xC8, 0x00],
        'rgbwGroup3NightMode' => [0xCA, 0x00],
        'rgbwGroup4NightMode' => [0xCC, 0x00],
        'rgbwBrightnessMax' => [0x4e, 0x1b],
        'rgbwBrightnessMin' => [0x4e, 0x02],
        'rgbwDiscoMode' => [0x4d, 0x00],
        'rgbwDiscoSlower' => [0x43, 0x00],
        'rgbwDiscoFaster' => [0x44, 0x00],
        'rgbwAllSetToWhite' => [0xc2, 0x00],
        'rgbwGroup0SetToWhite' => [0xc2, 0x00],
        'rgbwGroup1SetToWhite' => [0xc5, 0x00],
        'rgbwGroup2SetToWhite' => [0xc7, 0x00],
        'rgbwGroup3SetToWhite' => [0xc9, 0x00],
        'rgbwGroup4SetToWhite' => [0xcb, 0x00],
        'rgbwSetColorToViolet' => [0x40, 0x00],
        'rgbwSetColorToRoyalBlue' => [0x40, 0x10],
        'rgbwSetColorToBabyBlue' => [0x40, 0x20],
        'rgbwSetColorToAqua' => [0x40, 0x30],
        'rgbwSetColorToRoyalMint' => [0x40, 0x40],
        'rgbwSetColorToSeafoamGreen' => [0x40, 0x50],
        'rgbwSetColorToGreen' => [0x40, 0x60],
        'rgbwSetColorToLimeGreen' => [0x40, 0x70],
        'rgbwSetColorToYellow' => [0x40, 0x80],
        'rgbwSetColorToYellowOrange' => [0x40, 0x90],
        'rgbwSetColorToOrange' => [0x40, 0xa0],
        'rgbwSetColorToRed' => [0x40, 0xb0],
        'rgbwSetColorToPink' => [0x40, 0xc0],
        'rgbwSetColorToFusia' => [0x40, 0xd0],
        'rgbwSetColorToLilac' => [0x40, 0xe0],
        'rgbwSetColorToLavendar' => [0x40, 0xf0],

        // White Bulb commands
        'whiteAllOn' => [0x35, 0x00],
        'whiteAllOff' => [0x39, 0x00],
    'whiteGroup0On' => [0x35, 0x00],
    'whiteGroup0Off' => [0x39, 0x00],
        'whiteBrightnessUp' => [0x3c, 0x00],
        'whiteBrightnessDown' => [0x34, 0x00],
    'whiteGroup0BrightnessMax' => [0xb5, 0x00],
    'whiteGroup0NightMode' => [0xbb, 0x00],
        'whiteAllBrightnessMax' => [0xb5, 0x00],
        'whiteAllNightMode' => [0xbb, 0x00],
        'whiteWarmIncrease' => [0x3e, 0x00],
        'whiteCoolIncrease' => [0x3f, 0x00],
        'whiteGroup1On' => [0x38, 0x00],
        'whiteGroup1Off' => [0x3b, 0x00],
        'whiteGroup2On' => [0x3d, 0x00],
        'whiteGroup2Off' => [0x33, 0x00],
        'whiteGroup3On' => [0x37, 0x00],
        'whiteGroup3Off' => [0x3a, 0x00],
        'whiteGroup4On' => [0x32, 0x00],
        'whiteGroup4Off' => [0x36, 0x00],
        'whiteGroup1BrightnessMax' => [0xb8, 0x00],
        'whiteGroup2BrightnessMax' => [0xbd, 0x00],
        'whiteGroup3BrightnessMax' => [0xb7, 0x00],
        'whiteGroup4BrightnessMax' => [0xb2, 0x00],
        'whiteGroup1NightMode' => [0xbb, 0x00],
        'whiteGroup2NightMode' => [0xb3, 0x00],
        'whiteGroup3NightMode' => [0xba, 0x00],
        'whiteGroup4NightMode' => [0xb6, 0x00],
    ];

    /**
     * @param int $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    public function setRepeats($repeats)
    {
        $this->command_repeats = $repeats;
    }

    private function setActiveGroup($Group)
    {
        if ($Group < 0 || $Group > 4) {
            throw new \Exception('Active group must be between 0 and 4. 0 means all groups');
        }

        return $Group;
    }

    /**
     * @param int $rgbwActiveGroup
     *
     * @throws Exception
     */
    public function setRgbwActiveGroup($rgbwActiveGroup)
    {
        $this->rgbwActiveGroup = $this->setActiveGroup($rgbwActiveGroup);
    }

    // Same as setRgbwActiveGroup. Exists just to make method invocation easier according to the convention

    /**
     * @param int $rgbwActiveGroup
     *
     * @throws Exception
     */
    public function rgbwSetActiveGroup($rgbwActiveGroup)
    {
        $this->setRgbwActiveGroup($rgbwActiveGroup);
    }

    /**
     * @throws Exception
     *
     * @return int
     */
    public function getRgbwActiveGroup()
    {
        return $this->rgbwActiveGroup;
    }

    /**
     * @param int $whiteActiveGroup
     *
     * @throws Exception
     */
    public function setWhiteActiveGroup($whiteActiveGroup)
    {
        $this->whiteActiveGroup = $this->setActiveGroup($whiteActiveGroup);
    }

    // Same as setWhiteActiveGroup. Exists just to make method invocation easier according to the convention

    /**
     * @param int $whiteActiveGroup
     *
     * @throws Exception
     */
    public function whiteSetActiveGroup($whiteActiveGroup)
    {
        $this->setWhiteActiveGroup($whiteActiveGroup);
    }

    /**
     * @throws Exception
     *
     * @return int
     */
    public function getWhiteActiveGroup()
    {
        return $this->whiteActiveGroup;
    }

    public function __construct($host = '10.10.100.254', $port = 8899)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function sendCommand(array $command)
    {
        $command[] = 0x55; // last byte is always 0x55, will be appended to all commands
        $message = vsprintf(str_repeat('%c', count($command)), $command);
        for ($repetition = 0; $repetition < $this->command_repeats; ++$repetition) {
            if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
                socket_sendto($socket, $message, strlen($message), 0, $this->host, $this->port);
                socket_close($socket);
                usleep($this->getDelay()); //wait 100ms before sending next command
            }
        }
    }

    public function command($commandName)
    {
        $this->sendCommand($this->commandCodes[$commandName]);
    }

    public function rgbwSendOnToActiveGroup()
    {
        $this->rgbwSendOnToGroup($this->getRgbwActiveGroup());
    }

    public function rgbwSendOnToGroup($group)
    {
        $activeGroupOnCommand = 'rgbwGroup'.$group.'On';
        $this->command($activeGroupOnCommand);
    }

    public function rgbwSendOffToGroup($group)
    {
        $activeGroupOffCommand = 'rgbwGroup'.$group.'Off';
        $this->command($activeGroupOffCommand);
    }

    public function rgbwSetGroupToWhite($group)
    {
        $activeCommand = 'rgbwGroup'.$group.'SetToWhite';
        $this->command($activeCommand);
    }

    public function rgbwSetGroupToNightMode($group)
    {
        $this->rgbwSendOffToGroup($group);
        $activeCommand = 'rgbwGroup'.$group.'NightMode';
        $this->command($activeCommand);
    }

    public function whiteSendOnToGroup($group)
    {
        $activeGroupOnCommand = 'whiteGroup'.$group.'On';
        $this->command($activeGroupOnCommand);
    }

    public function whiteSendOffToGroup($group)
    {
        $activeGroupOffCommand = 'whiteGroup'.$group.'Off';
        $this->command($activeGroupOffCommand);
    }

    public function whiteSetGroupToNightMode($group)
    {
        $activeCommand = 'whiteGroup'.$group.'NightMode';
        $this->command($activeCommand);
    }

    public function whiteSendOnToActiveGroup()
    {
        $this->whiteSendOnToGroup($this->getWhiteActiveGroup());
    }

    public function rgbwAllOn()
    {
        $this->command('rgbwAllOn');
    }

    public function rgbwAllOff()
    {
        $this->command('rgbwAllOff');
    }

    public function rgbwGroup1On()
    {
        $this->command('rgbwGroup1On');
    }

    public function rgbwGroup2On()
    {
        $this->command('rgbwGroup2On');
    }

    public function rgbwGroup3On()
    {
        $this->command('rgbwGroup3On');
    }

    public function rgbwGroup4On()
    {
        $this->command('rgbwGroup4On');
    }

    public function rgbwGroup1Off()
    {
        $this->command('rgbwGroup1Off');
    }

    public function rgbwGroup2Off()
    {
        $this->command('rgbwGroup2Off');
    }

    public function rgbwGroup3Off()
    {
        $this->command('rgbwGroup3Off');
    }

    public function rgbwGroup4Off()
    {
        $this->command('rgbwGroup4Off');
    }

    public function rgbwAllNightMode()
    {
        $this->rgbwAlloff();
        $this->command('rgbwAllNightMode');
    }

    public function rgbwGroup1NightMode()
    {
        $this->rgbwGroup1off();
        $this->command('rgbwGroup1NightMode');
    }

    public function rgbwGroup2NightMode()
    {
        $this->rgbwGroup2off();
        $this->command('rgbwGroup2NightMode');
    }

    public function rgbwGroup3NightMode()
    {
        $this->rgbwGroup3off();
        $this->command('rgbwGroup3NightMode');
    }

    public function rgbwGroup4NightMode()
    {
        $this->rgbwGroup4off();
        $this->command('rgbwGroup4NightMode');
    }

    public function rgbwBrightnessMax()
    {
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMax');
    }

    public function rgbwBrightnessMin()
    {
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMin');
    }

    public function rgbwAllBrightnessMin()
    {
        $this->setRgbwActiveGroup(0);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMin');
    }

    public function rgbwAllBrightnessMax()
    {
        $this->setRgbwActiveGroup(0);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMax');
    }

    public function rgbwGroup1BrightnessMax()
    {
        $this->setRgbwActiveGroup(1);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMax');
    }

    public function rgbwGroup2BrightnessMax()
    {
        $this->setRgbwActiveGroup(2);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMax');
    }

    public function rgbwGroup3BrightnessMax()
    {
        $this->setRgbwActiveGroup(3);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMax');
    }

    public function rgbwGroup4BrightnessMax()
    {
        $this->setRgbwActiveGroup(4);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMax');
    }

    public function rgbwGroup1BrightnessMin()
    {
        $this->setRgbwActiveGroup(1);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMin');
    }

    public function rgbwGroup2BrightnessMin()
    {
        $this->setRgbwActiveGroup(2);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMin');
    }

    public function rgbwGroup3BrightnessMin()
    {
        $this->setRgbwActiveGroup(3);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMin');
    }

    public function rgbwGroup4BrightnessMin()
    {
        $this->setRgbwActiveGroup(4);
        $this->rgbwSendOnToActiveGroup();
        $this->command('rgbwBrightnessMin');
    }

    public function rgbwBrightnessPercent($brightnessPercent, $group = null)
    {
        $realBrightnessPercent = $brightnessPercent;
        if ($brightnessPercent < 10) {
            $brightnessPercent = 10;
        }
        if ($brightnessPercent > 100) {
            $brightnessPercent = 100;
        }
        $brightnessPercent = round(2 + (($brightnessPercent / 100) * 25));
        $group = isset($group) ? $group : $this->getRgbwActiveGroup();
        $this->rgbwSendOnToGroup($group);
        $this->sendCommand([0x4e, $brightnessPercent]);

        if (null !== $group) {
            $file = sprintf('/dev/shm/milight-brightness-%d', $group);
            if (!is_file($file)) {
                touch($file);
            }
            file_put_contents($file, $realBrightnessPercent);
        }
    }

    public function rgbwBrightnessDown($group = null)
    {
        if (!$group) {
            $groups = [1, 2, 3, 4];
        } else {
            $groups = [$group];
        }

        foreach ($groups as $group) {
            $file = sprintf('/dev/shm/milight-brightness-%d', $group);
            if (!is_file($file)) {
                $brightnessPercent = 99;
            } else {
                $brightnessPercent = (int) trim(file_get_contents($file)) - 5;
            }

            $this->rgbwBrightnessPercent($brightnessPercent, $group);
        }
    }

    public function rgbwBrightnessUp($group = null)
    {
        if (null === $group) {
            $groups = [1, 2, 3, 4];
        } else {
            $groups = [$group];
        }

        foreach ($groups as $group) {
            $file = sprintf('/dev/shm/milight-brightness-%d', $group);
            if (!is_file($file)) {
                $brightnessPercent = 100;
            } else {
                $brightnessPercent = (int) trim(file_get_contents($file)) + 5;
            }

            if ($brightnessPercent > 100) {
                $brightnessPercent = 100;
            }

            $this->rgbwBrightnessPercent($brightnessPercent, $group);
        }
    }

    public function rgbwDiscoMode($group = null)
    {
        if ($group) {
            $this->setRgbwActiveGroup($group);
            $this->rgbwSendOnToGroup($group);
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwDiscoMode');
    }

    public function rgbwDiscoSlower($group = null)
    {
        if ($group) {
            $this->setRgbwActiveGroup($group);
            $this->rgbwSendOnToGroup($group);
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwDiscoSlower');
    }

    public function rgbwDiscoFaster($group = null)
    {
        if ($group) {
            $this->setRgbwActiveGroup($group);
            $this->rgbwSendOnToGroup($group);
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwDiscoFaster');
    }

    public function rgbwAllSetToWhite()
    {
        $this->command('rgbwAllSetToWhite');
    }

    public function rgbwGroup1SetToWhite()
    {
        $this->command('rgbwGroup1SetToWhite');
    }

    public function rgbwGroup2SetToWhite()
    {
        $this->command('rgbwGroup2SetToWhite');
    }

    public function rgbwGroup3SetToWhite()
    {
        $this->command('rgbwGroup3SetToWhite');
    }

    public function rgbwGroup4SetToWhite()
    {
        $this->command('rgbwGroup4SetToWhite');
    }

    public function rgbwSetColorToViolet()
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }

        $this->command('rgbwSetColorToViolet');
    }

    public function rgbwSetColorToRoyalBlue($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToRoyalBlue');
    }

    public function rgbwSetColorToBabyBlue($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToBabyBlue');
    }

    /**
     * Sets color of the currently active group to white.
     */
    public function rgbwSetColorToWhite()
    {
        if ($this->getActiveGroup() == 0) {
            $this->rgbwAllSetToWhite();

            return;
        }
        $this->command('rgbwGroup'.strval($this->getActiveGroup()).'SetToWhite');
    }

    public function rgbwSetColorToAqua($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToAqua');
    }

    public function rgbwSetColorToRoyalMint($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToRoyalMint');
    }

    public function rgbwSetColorToSeafoamGreen($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToSeafoamGreen');
    }

    public function rgbwSetColorToGreen($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToGreen');
    }

    public function rgbwSetColorToLimeGreen($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToLimeGreen');
    }

    public function rgbwSetColorToYellow($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToYellow');
    }

    public function rgbwSetColorToYellowOrange($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToYellowOrange');
    }

    public function rgbwSetColorToOrange($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToOrange');
    }

    public function rgbwSetColorToRed($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToRed');
    }

    public function rgbwSetColorToPink($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToPink');
    }

    public function rgbwSetColorToFusia($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToFusia');
    }

    public function rgbwSetColorToLilac($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToLilac');
    }

    public function rgbwSetColorToLavendar($group = null)
    {
        if (null !== $group) {
            $commandName = sprintf('rgbwGroup%dOn', (int) $group);
            $this->$commandName();
        } else {
            $this->rgbwSendOnToActiveGroup();
        }
        $this->command('rgbwSetColorToLavendar');
    }

    public function whiteAllOn()
    {
        $this->command('whiteAllOn');
    }

    public function whiteAllOff()
    {
        $this->command('whiteAllOff');
    }

    public function whiteBrightnessUp($group = null)
    {
        if (null !== $group) {
            $this->whiteSetActiveGroup($group);
        }
        $this->whiteSendOnToActiveGroup();
        $this->command('whiteBrightnessUp');
    }

    public function whiteBrightnessDown($group = null)
    {
        if (null !== $group) {
            $this->whiteSetActiveGroup($group);
        }
        $this->whiteSendOnToActiveGroup();
        $this->command('whiteBrightnessDown');
    }

    public function whiteAllBrightnessMax()
    {
        $this->command('whiteAllBrightnessMax');
    }

    public function whiteAllBrightnessMin()
    {
        $this->setWhiteActiveGroup(0);
        $this->whiteSendOnToActiveGroup();
        for ($i = 0; $i < 10; ++$i) {
            $this->command('whiteBrightnessDown');
        }
    }

    public function whiteAllNightMode()
    {
        $this->command('whiteAllNightMode');
    }

    public function whiteWarmIncrease()
    {
        $this->whiteSendOnToActiveGroup();
        $this->command('whiteWarmIncrease');
    }

    public function whiteCoolIncrease()
    {
        $this->whiteSendOnToActiveGroup();
        $this->command('whiteCoolIncrease');
    }

    public function whiteGroup1On()
    {
        $this->command('whiteGroup1On');
    }

    public function whiteGroup1Off()
    {
        $this->command('whiteGroup1Off');
    }

    public function whiteGroup2On()
    {
        $this->command('whiteGroup2On');
    }

    public function whiteGroup2Off()
    {
        $this->command('whiteGroup2Off');
    }

    public function whiteGroup3On()
    {
        $this->command('whiteGroup3On');
    }

    public function whiteGroup3Off()
    {
        $this->command('whiteGroup3Off');
    }

    public function whiteGroup4On()
    {
        $this->command('whiteGroup4On');
    }

    public function whiteGroup4Off()
    {
        $this->command('whiteGroup4Off');
    }

    public function whiteGroup1BrightnessMax()
    {
        $this->command('whiteGroup1BrightnessMax');
    }

    public function whiteGroup2BrightnessMax()
    {
        $this->command('whiteGroup2BrightnessMax');
    }

    public function whiteGroup3BrightnessMax()
    {
        $this->command('whiteGroup3BrightnessMax');
    }

    public function whiteGroup4BrightnessMax()
    {
        $this->command('whiteGroup4BrightnessMax');
    }

    public function whiteGroup1BrightnessMin()
    {
        $this->setWhiteActiveGroup(1);
        $this->whiteSendOnToActiveGroup();
        for ($i = 0; $i < 10; ++$i) {
            $this->command('whiteBrightnessDown');
        }
    }

    public function whiteGroup2BrightnessMin()
    {
        $this->setWhiteActiveGroup(2);
        $this->whiteSendOnToActiveGroup();
        for ($i = 0; $i < 10; ++$i) {
            $this->command('whiteBrightnessDown');
        }
    }

    public function whiteGroup3BrightnessMin()
    {
        $this->setWhiteActiveGroup(3);
        $this->whiteSendOnToActiveGroup();
        for ($i = 0; $i < 10; ++$i) {
            $this->command('whiteBrightnessDown');
        }
    }

    public function whiteGroup4BrightnessMin()
    {
        $this->setWhiteActiveGroup(4);
        $this->whiteSendOnToActiveGroup();
        for ($i = 0; $i < 10; ++$i) {
            $this->command('whiteBrightnessDown');
        }
    }

    public function whiteGroup1NightMode()
    {
        $this->command('whiteGroup1NightMode');
    }

    public function whiteGroup2NightMode()
    {
        $this->command('whiteGroup2NightMode');
    }

    public function whiteGroup3NightMode()
    {
        $this->command('whiteGroup3NightMode');
    }

    public function whiteGroup4NightMode()
    {
        $this->command('whiteGroup4NightMode');
    }

    public function rgbwSetColorHsv(array $hsvColor)
    {
        $milightColor = $this->hslToMilightColor($hsvColor);
        $activeGroupOnCommand = 'rgbwGroup'.$this->getRgbwActiveGroup().'On';
        $this->command($activeGroupOnCommand);
        $this->sendCommand([0x40, $milightColor]);
    }

    public function rgbwSetColorHexString($color)
    {
        $rgb = $this->rgbHexToIntArray($color);
        $hsl = $this->rgbToHsl($rgb[0], $rgb[1], $rgb[2]);
        $milightColor = $this->hslToMilightColor($hsl);
        $this->rgbwSendOnToActiveGroup();
        $this->sendCommand([0x40, $milightColor]);
    }

    public function rgbHexToIntArray($hexColor)
    {
        $hexColor = ltrim($hexColor, '#');

        $hexColorLenghth = strlen($hexColor);
        if ($hexColorLenghth != 8 && $hexColorLenghth != 6) {
            throw new \Exception('Color hex code must match 8 or 6 characters');
        }
        if ($hexColorLenghth == 8) {
            $r = hexdec(substr($hexColor, 2, 2));
            $g = hexdec(substr($hexColor, 4, 2));
            $b = hexdec(substr($hexColor, 6, 2));
            if (($r == 0 && $g == 0 && $b == 0) || ($r == 255 && $g == 255 && $b == 255)) {
                throw new \Exception('Color cannot be black or white');
            }

            return [$r, $g, $b];
        }

        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        if (($r == 0 && $g == 0 && $b == 0) || ($r == 255 && $g == 255 && $b == 255)) {
            throw new \Exception('Color cannot be black or white');
        }

        return [$r, $g, $b];
    }

    public function rgbToHsl($r, $g, $b)
    {
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        $h = '';

        if ($d == 0) {
            $h = $s = 0;
        } else {
            $s = $d / (1 - abs(2 * $l - 1));

            switch ($max) {
                case $r:
                    $h = 60 * fmod((($g - $b) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * (($b - $r) / $d + 2);
                    break;

                case $b:
                    $h = 60 * (($r - $g) / $d + 4);
                    break;
            }
        }

        return [$h, $s, $l];
    }

    public function hslToMilightColor($hsl)
    {
        $color = (256 + 176 - (int) ($hsl[0] / 360.0 * 255.0)) % 256;

        return $color + 0xfa;
    }
}
