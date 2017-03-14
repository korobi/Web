<?php

use Symfony\Component\HttpFoundation\Request;

if (inMaintenance()) {
    http_response_code(503);
    require_once 'maintenance.php';
    exit;
}

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__.'/../app/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new AppKernel('prod', false);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

/**
 * @return bool
 */
function inMaintenance() {
    return file_exists(__DIR__ . '/../src/Korobi/WebBundle/maintenance');
}
