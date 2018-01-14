<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
use ApiRecipe\Manager\RoutingManager;

include sprintf('%s/../vendor/autoload.php', __DIR__);
header('Access-Control-Allow-Origin: *');

$routing = new RoutingManager();
