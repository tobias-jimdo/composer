<?php

namespace Composer\Downloader;

use Composer\Package\PackageInterface;

class LocalDownloader implements DownloaderInterface
{

    /**
     * Returns installation source (either source or dist).
     *
     * @return string "source" or "dist"
     */
    public function getInstallationSource()
    {
        return 'dist';
    }

    /**
     * Downloads specific package into specific folder.
     *
     * @param PackageInterface $package package instance
     * @param string $path    download path
     */
    public function download(PackageInterface $package, $path)
    {
        rmdir($path);
        symlink($package->getDistUrl(), $path);
    }

    /**
     * Updates specific package in specific folder from initial to target version.
     *
     * @param PackageInterface $initial initial package
     * @param PackageInterface $target  updated package
     * @param string $path    download path
     */
    public function update(PackageInterface $initial, PackageInterface $target, $path)
    {

    }

    /**
     * Removes specific package from specific folder.
     *
     * @param PackageInterface $package package instance
     * @param string $path    download path
     */
    public function remove(PackageInterface $package, $path)
    {
        unlink($path);
    }

    /**
     * Sets whether to output download progress information or not
     *
     * @param  bool $outputProgress
     * @return DownloaderInterface
     */
    public function setOutputProgress($outputProgress)
    {
        // TODO: Implement setOutputProgress() method.
    }
}