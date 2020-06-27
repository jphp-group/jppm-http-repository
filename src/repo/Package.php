<?php

namespace repo;

use php\io\File;
use php\io\IOException;
use php\lib\fs;

class Package {
    private string $name;
    private array $versions = [];

    /**
     * Package constructor.
     * @param File $packageDirectory
     * @throws IOException
     */
    public function __construct(File $packageDirectory) {
        $this->name = fs::name($packageDirectory);

        $dirs = $packageDirectory->find();
        foreach ($dirs as $dir) {
            $versionDir = new File($packageDirectory, $dir);
            if ($versionDir->isFile())
                continue;

            var_dump($versionDir->getAbsolutePath());

            $size = 0;
            fs::scan($versionDir, function (File $file) use (&$size) {
                if ($file->isFile())
                    return;

                $size += fs::size($file);
            });

            $this->versions[fs::name($versionDir)] = [
                "hash" => null,
                "size" => $size,
                "sha256" => null // TODO: make sha256 hash of package!
            ];
        }
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return [
            "name" => $this->name,
            "versions" => $this->versions
        ];
    }
}
