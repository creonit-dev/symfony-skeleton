<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../app/autoload.php';

if ('dev' === getenv('SYMFONY_ENV')) {
    Debug::enable();

    $kernel = new AppKernel('dev', true);
    $kernel->loadClassCache();

} else {

    include_once __DIR__ . '/../var/bootstrap.php.cache';

    $kernel = new AppKernel('prod', false);
    $kernel->loadClassCache();
    //$kernel = new AppCache($kernel);
}

$request = Request::createFromGlobals();

Request::setTrustedProxies(
    ['127.0.0.1', $request->server->get('REMOTE_ADDR')],
    Request::HEADER_X_FORWARDED_ALL
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
