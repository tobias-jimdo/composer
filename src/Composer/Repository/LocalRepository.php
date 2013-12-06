<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Repository;

use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Loader\ArrayLoader;

class LocalRepository extends ArrayRepository
{
    /** @var LoaderInterface */
    protected $loader;

    protected $lookup;

    public function __construct(array $repoConfig, IOInterface $io)
    {
        $this->loader = new ArrayLoader();
        $this->lookup = $repoConfig['url'];
        $this->io = $io;
    }

    protected function initialize()
    {
        parent::initialize();

        $this->addPackageToRepository($this->lookup);
    }

    private function addPackageToRepository($path)
    {
        $io = $this->io;

        $file = new \SplFileInfo($path . '/composer.json');

        /* @var $file \SplFileInfo */
        if (!$file->isFile()) {
            if ($io->isVerbose()) {
                $io->write("Folder <comment>{$path}</comment> doesn't contain a composer.json");
            }
            return;
        }

        $package = $this->getComposerInformation($file);
        if (!$package) {
            if ($io->isVerbose()) {
                $io->write("File <comment>{$file->getBasename()}</comment> doesn't seem to hold a package");
            }
            return;
        }

        if ($io->isVerbose()) {
            $template = 'Found package <info>%s</info> (<comment>%s</comment>) in folder <info>%s</info>';
            $io->write(sprintf($template, $package->getName(), $package->getPrettyVersion(), $path));
        }

        $this->addPackage($package);
    }

    private function getComposerInformation(\SplFileInfo $file)
    {
        $json = file_get_contents($file->getPathname());

        $package = JsonFile::parseJson($json, $file->getPathname());
        $package['dist'] = array(
            'type' => 'local',
            'url' => $file->getPath(),
            'reference' => $file->getBasename(),
            'shasum' => sha1_file($file->getBasename())
        );

        $package = $this->loader->load($package);

        return $package;
    }
}
