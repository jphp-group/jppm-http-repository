<?php

namespace utils;

use php\io\File;
use php\io\IOException;
use php\lang\System;
use repo\Package;

class LocalRepository {

    /**
     * @return File
     */
    public static function getLocalRepositoryDirectory(): File {
        return new File(System::getProperty("user.home")
            . File::DIRECTORY_SEPARATOR
            . ".jppm"
            . File::DIRECTORY_SEPARATOR
            . "repo");
    }

    /**
     * @return Package[]
     * @throws IOException
     */
    public static function getAllPackages(): array {
        $packages = [];

        $repo = self::getLocalRepositoryDirectory();
        $dirs = $repo->find(fn(File $file) => $file->isDirectory());
        foreach ($dirs as $dir)
            $packages[] = new Package(new File($repo, $dir));

        return $packages;
    }

    /**
     * @param string $name
     * @return Package|null
     * @throws IOException
     */
    public static function getPackageByName(string $name): ?Package {
        $repo = self::getLocalRepositoryDirectory();
        $packageFile = new File($repo, $name);

        if (!$packageFile->isDirectory())
            return null;

        return new Package($packageFile);
    }
}
