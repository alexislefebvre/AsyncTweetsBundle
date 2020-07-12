<?php

use Acme\App\AppKernel;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/tests/App/bootstrap.php';

umask(0000);

$kernel = new AppKernel('test', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
