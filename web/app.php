<?php

use Symfony\Component\HttpFoundation\Request;

if (!inMaintenance()) {
    $loader = require_once __DIR__.'/../app/bootstrap.php.cache';

    // NOTE: ApcClassLoader should not be used; apc is dead

    require_once __DIR__ . '/../app/AppKernel.php';
    //require_once __DIR__.'/../app/AppCache.php';

    $kernel = new AppKernel('prod', false);
    $kernel->loadClassCache();
    //$kernel = new AppCache($kernel);

    // When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
    //Request::enableHttpMethodParameterOverride();
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} else {
    http_response_code(503);
    require_once 'maintenance.php';
    exit;
}

/**
 * @return bool
 */
function inMaintenance() {
    return file_exists(__DIR__ . '/../src/Korobi/WebBundle/maintenance');
}
