<?php

namespace repo;

use php\io\File;
use php\io\IOException;
use php\lib\fs;

class Package {
    private string $name;
    private File $packageDirectory;
    private array $versions = [];

    /**
     * Package constructor.
     * @param File $packageDirectory
     * @throws IOException
     */
    public function __construct(File $packageDirectory) {
        $this->packageDirectory = $packageDirectory;
        $this->name = fs::name($this->packageDirectory);

        $dirs = $this->packageDirectory->find();
        foreach ($dirs as $dir) {
            $versionDir = new File($this->packageDirectory, $dir);
            if ($versionDir->isFile())
                continue;

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
     * @param string $version
     * @return bool
     */
    public function hasVersion(string $version): bool {
        return isset($this->versions[$version]);
    }

    /**
     * @param string $version
     * @return File|null
     */
    public function getPackageDirectoryByVersion(string $version): ?File {
        if (!$this->hasVersion($version))
            return null;

        return new File($this->packageDirectory, $version);
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
