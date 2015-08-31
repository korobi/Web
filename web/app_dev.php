<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
/*if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}*/

if (!inMaintenance()) {
    if (!array_key_exists('REMOTE_ADDR', $_SERVER) || array_key_exists('REMOTE_ADDR', $_SERVER) && !isInternalIpAddress($_SERVER['REMOTE_ADDR'])) {
        if (!isset($_SERVER['HTTP_X_GITHUB_DELIVERY']) && !isset($_SERVER['HTTP_X_HUB_SIGNATURE']) && strpos($_SERVER['HTTP_USER_AGENT'], 'GitHub-Hookshot/') === false) {
            // It looks like a user
            if (!isset($_SERVER['HTTP_X_KOROBI_AUTH']) || $_SERVER['HTTP_X_KOROBI_AUTH'] != 'nkYPUztAKf3gv82FnuMd9BB') {
                http_response_code(403);
                exit();
            }
        } else {
            // Allow GitHub to deploy
            if (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) !== '/deploy/') {
                http_response_code(403);
                exit('Oops, you look like a GitHub but you\'re not requesting a deploy?');
            }
        }
    }

    $loader = require_once __DIR__ . '/../var/bootstrap.php.cache';
    Debug::enable();

    require_once __DIR__ . '/../app/AppKernel.php';

    $kernel = new AppKernel('dev', true);
    $kernel->loadClassCache();
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

/**
 * @param $ip
 * @return bool
 */
function isInternalIpAddress($ip) {
    // 10.0.0.0/8, 192.168.0.0/16, 172.16.0.0 - 172.31.255.255
    return in_array($ip, ['127.0.0.1', 'fe80::1', '::1']) ||
        preg_match('/^10\.|^192\.168\.|^172\.1[6-9]\.|^172\.2[0-9]\.|^172\.3[01]\./', $ip);
}
