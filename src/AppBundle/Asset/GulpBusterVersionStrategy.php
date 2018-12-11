<?php

namespace AppBundle\Asset;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class GulpBusterVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string
     */
    private $manifestPath;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string[]
     */
    private $hashes;

    /*
     * @var string
     */
    private $fallbackVersion;

    /**
     * @param string $manifestPath
     * @param string|null $format
     * @param string $fallbackVersion
     */
    public function __construct($manifestPath, $format = null, $fallbackVersion = '')
    {
        $this->manifestPath = $manifestPath;
        $this->format = $format ?: '%s?%s';
        $this->fallbackVersion = $fallbackVersion;
    }

    public function getVersion($path)
    {
        if (!is_array($this->hashes)) {
            $this->hashes = $this->loadManifest();
        }

        return isset($this->hashes[$path]) ? $this->hashes[$path] : $this->fallbackVersion;
    }

    public function applyVersion($path)
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        $versionized = sprintf($this->format, ltrim($path, '/'), $version);

        if ($path && '/' === $path[0]) {
            return '/' . $versionized;
        }

        return $versionized;
    }

    private function loadManifest()
    {
        return is_file($this->manifestPath) ? json_decode(file_get_contents($this->manifestPath), true) : [];
    }
}