<?php

use Homatisation\Manager\RoutingManager;

include sprintf('%s/../vendor/autoload.php', __DIR__);
header('Access-Control-Allow-Origin: *');

$routing = new RoutingManager();
