<?php

use api\repo\RepoDownloadHandler;
use api\repo\RepoFindHandler;
use api\repo\RepoGetHandler;
use php\http\HttpServer;
use php\http\HttpServerRequest;
use php\lang\System;
use php\lib\str;
use php\net\ServerSocket;

function format(HttpServerRequest $req): string {
    $query = "";

    foreach ($req->queryParameters() as $key => $value)
        $query .= "$key=$value, ";

    if ($query != "") {
        $query = " [" . str::sub($query, 0, str::length($query) - 2) . "]";
    }

    return $req->protocol() . " " .$req->method() . " " . $req->path() . $query . "\n";
}

$port = System::getProperty('web.server.port') ?: 8080;

while (!ServerSocket::isAvailableLocalPort($port))
    $port++;

$server = new HttpServer($port);
$server->addHandler(fn($req, $res) => System::out()->write(format($req)));

$server->get("/repo/find", new RepoFindHandler());
$server->get("/repo/get", new RepoGetHandler());
$server->get("/repo/download/{name}.tar.gz", new RepoDownloadHandler());

echo "Starting JPPM repository at port $port ... \n";

$server->stopAtShutdown(true);
$server->run();
