<?php

namespace api\repo;

use compress\GzipOutputStream;
use compress\TarArchive;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use php\io\File;
use php\io\IOException;
use php\lib\fs;
use utils\LocalRepository;

/**
 * Class RepoDownloadHandler
 * @package api\repo
 */
class RepoDownloadHandler
{

    /**
     * @param HttpServerRequest $request
     * @param HttpServerResponse $response
     * @throws IOException
     */
    public function __invoke(HttpServerRequest $request, HttpServerResponse $response) {
        $name = $request->attribute("name");
        $version = $request->param("version");
        if ($version == null || $name == null) {
            $response->status(400);
            $response->write(json_encode(["error" => "Bad request!"]));
            return;
        }

        $package = LocalRepository::getPackageByName($name);
        if ($package == null || !$package->hasVersion($version)) {
            $response->status(404);
            $response->write(json_encode(["error" => "Package not found!"]));
            return;
        }

        $tar = new TarArchive(new GzipOutputStream($response->bodyStream()));
        $tar->open();

        $packageDirectory = $package->getPackageDirectoryByVersion($version);
        fs::scan($packageDirectory, function (File $file) use ($packageDirectory, $tar) {
            if ($file->isDirectory())
                return;

            $tar->addFile($file, fs::relativize($file, $packageDirectory));
        });

        $tar->close();
    }
}
