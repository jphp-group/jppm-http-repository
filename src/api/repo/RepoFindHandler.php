<?php

namespace api\repo;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use php\io\IOException;
use utils\LocalRepository;

/**
 * Class RepoFindHandler
 * @package repo
 */
class RepoFindHandler {

    /**
     * @param HttpServerRequest $request
     * @param HttpServerResponse $response
     * @throws IOException
     */
    public function __invoke(HttpServerRequest $request, HttpServerResponse $response) {
        $name = $request->param("name");
        if ($name == null) {
            $response->status(400);
            $response->write(json_encode([ "error" => "Bad request!" ]));
            return;
        }

        $package = LocalRepository::getPackageByName($name);
        if ($package == null) {
            $response->status(404);
            $response->write(json_encode([ "error" => "Package not found!" ]));
            return;
        }

        $response->write(json_encode($package->toArray()));
    }
}
