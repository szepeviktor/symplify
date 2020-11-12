<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\Doctrine\ValueObject;

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder;
use Symplify\Autodiscovery\ValueObject\NamespaceDetector;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
final class DoctrineEntityMappingAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;
    /**
     * @var \Symplify\Autodiscovery\ValueObject\NamespaceDetector
     */
    private $namespaceDetector;
    /**
     * @var \Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder
     */
    private $autodiscoveryFinder;
    public function __construct(\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, AutodiscoveryFinder $autodiscoveryFinder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->namespaceDetector = new NamespaceDetector(new SmartFileSystem());
        $this->autodiscoveryFinder = $autodiscoveryFinder;
    }
    /**
     * Needs to run before @see \Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass
     */
    public function autodiscover(): void
    {
        $entityMappings = [];
        $entityDirectories = $this->autodiscoveryFinder->getEntityDirectories();
        foreach ($entityDirectories as $entityDirectory) {
            $namespace = $this->namespaceDetector->detectFromDirectory($entityDirectory);
            if (!$namespace) {
                continue;
            }
            $entityMappings[] = [
                // required name
                'name' => $namespace,
                'prefix' => $namespace,
                'type' => 'annotation',
                'dir' => $entityDirectory->getRealPath(),
                // performance
                'is_bundle' => false,
            ];
        }
        $xmlNamespaces = [];
        $directoryByNamespace = $this->resolveDirectoryByNamespace($this->autodiscoveryFinder->getEntityXmlFiles());
        foreach ($directoryByNamespace as $namespace => $directory) {
            if (in_array($namespace, $xmlNamespaces, true)) {
                continue;
            }
            $xmlNamespaces[] = $namespace;
            $entityMappings[] = [
                // required name
                'name' => $namespace,
                'prefix' => $namespace,
                'type' => 'xml',
                'dir' => $directory,
                'is_bundle' => false,
            ];
        }
        if (count($entityMappings) === 0) {
            return;
        }
        // @see https://symfony.com/doc/current/reference/configuration/doctrine.html#mapping-entities-outside-of-a-bundle
        $this->containerBuilder->prependExtensionConfig('doctrine', ['orm' => ['mappings' => $entityMappings]]);
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $entityXmlFiles
     * @return int[]|string[]
     */
    private function resolveDirectoryByNamespace(array $entityXmlFiles): array
    {
        $filesByDirectory = $this->groupFileInfosByDirectory($entityXmlFiles);
        $directoryByNamespace = [];
        foreach ($filesByDirectory as $directory => $filesInDirectory) {
            $commonNamespace = $this->resolveCommonNamespaceForXmlFileInfos($filesInDirectory);
            /** @var string $directory */
            $directoryByNamespace[$commonNamespace] = $directory;
        }
        return $directoryByNamespace;
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $smartFileInfos
     * @return array<string, \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]>
     */
    private function groupFileInfosByDirectory(array $smartFileInfos): array
    {
        $filesByDirectory = [];
        foreach ($smartFileInfos as $entityXmlFileInfo) {
            $filesByDirectory[$entityXmlFileInfo->getPath()][] = $entityXmlFileInfo;
        }
        return $filesByDirectory;
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $xmlFileInfos
     */
    private function resolveCommonNamespaceForXmlFileInfos(array $xmlFileInfos): string
    {
        $namespaces = [];
        foreach ($xmlFileInfos as $xmlFileInfo) {
            $namespace = $this->namespaceDetector->detectFromXmlFileInfo($xmlFileInfo);
            if ($namespace) {
                $namespaces[] = $namespace;
            }
        }
        $commonNamespace = Strings::findPrefix($namespaces);
        return rtrim($commonNamespace, '\\');
    }
}
