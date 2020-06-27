<?php

namespace api\repo;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use utils\LocalRepository;

/**
 * Class RepoGetHandler
 * @package api\repo
 */
class RepoGetHandler {

    /**
     * @param HttpServerRequest $request
     * @param HttpServerResponse $response
     */
    public function __invoke(HttpServerRequest $request, HttpServerResponse $response) {
        $name = $request->param("name");
        $version = $request->param("version");
        $host = $request->header("Host");
        $scheme = $request->header("Scheme") ?: "http";
        if ($version == null || $name == null || $host == null) {
            $response->status(400);
            $response->write(json_encode([ "error" => "Bad request!" ]));
            return;
        }

        $package = LocalRepository::getPackageByName($name);
        if ($package == null || !$package->hasVersion($version)) {
            $response->status(404);
            $response->write(json_encode([ "error" => "Package not found!" ]));
            return;
        }

        $response->write(json_encode([
            "name" => $package->getName(),
            "version" => $version,
            "downloadUrl" => "{$scheme}://{$host}/repo/download/{$package->getName()}.tar.gz?version=$version"
        ]));
    }
}
