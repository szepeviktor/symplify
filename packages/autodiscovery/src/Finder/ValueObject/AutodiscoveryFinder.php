<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\Finder\ValueObject;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\ValueObject\FinderSanitizer;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class AutodiscoveryFinder
{
    /**
     * @var string
     */
    private $projectDirectory;
    /**
     * @var string[]
     */
    private $packageDirectories = [];
    /**
     * @var \Symplify\SmartFileSystem\Finder\ValueObject\FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @param string[] $packageDirectories
     */
    public function __construct(string $projectDirectory, array $packageDirectories = [])
    {
        $this->finderSanitizer = new FinderSanitizer();
        $this->projectDirectory = $projectDirectory;
        $this->packageDirectories = $packageDirectories;
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getTemplatesDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('templates');
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getEntityDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('Entity');
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getControllerDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('Controller');
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getTranslationDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('translations');
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getEntityXmlFiles(): array
    {
        // for orm|dcm masks @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/xml-mapping.html
        return $this->getFilesInSourceByName('#\.(orm|dcm)\.xml$#');
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    private function getDirectoriesInSourceByName(string $name): array
    {
        if ($this->getDirectories() === []) {
            return [];
        }
        $finder = Finder::create()->directories()->name($name)->in($this->getDirectories());
        // include "tests" skip in tests
        if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
            $finder->notPath('#tests#');
        }
        return $this->finderSanitizer->sanitize($finder);
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    private function getFilesInSourceByName(string $name): array
    {
        if ($this->getDirectories() === []) {
            return [];
        }
        $finder = Finder::create()->files()->name($name)->in($this->getDirectories());
        // include "tests" skip in tests
        if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
            $finder->notPath('#tests#');
        }
        return $this->finderSanitizer->sanitize($finder);
    }
    /**
     * @return string[]
     */
    private function getDirectories(): array
    {
        $possibleDirectories = [$this->projectDirectory . '/src', $this->projectDirectory . '/templates', $this->projectDirectory . '/translations', $this->projectDirectory . '/packages', $this->projectDirectory . '/projects'];
        $possibleDirectories = array_merge($possibleDirectories, $this->packageDirectories);
        return array_filter($possibleDirectories, 'file_exists');
    }
}
