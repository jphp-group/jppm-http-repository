<?php

use php\http\HttpServer;
use php\lang\System;
use php\net\ServerSocket;
use api\repo\RepoFindHandler;

$port = System::getProperty('web.server.port') ?: 8080;

while (!ServerSocket::isAvailableLocalPort($port))
    $port++;

$server = new HttpServer($port);
$server->addHandler(fn($req, $res) => System::out()->write($req->protocol() . " " .$req->method() . " " . $req->path() . "\n"));

$server->get("/repo/find", new RepoFindHandler());

echo "Starting JPPM repository at port $port ... \n";

$server->stopAtShutdown(true);
$server->run();
