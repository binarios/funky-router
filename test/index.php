<?php

define('ROOT', dirname(__DIR__,1));
define('VENDOR', ROOT . '/vendor');

require_once VENDOR . '/autoload.php';

$funkyRouter = new FunkyRouter\Router();
# nur für Testzwecke
$funkyRouter->setRequestMethod('GET');
$funkyRouter->setRequestUri('/demo/uri/test-123');


$funkyRouter->dispatch();