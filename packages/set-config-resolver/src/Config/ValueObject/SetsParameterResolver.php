<?php

declare(strict_types=1);
namespace Symplify\SetConfigResolver\Config\ValueObject;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symplify\SetConfigResolver\ValueObject\SetResolver;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class SetsParameterResolver
{
    /**
     * @var string
     */
    private const SETS = 'sets';
    /**
     * @var \Symplify\SetConfigResolver\ValueObject\SetResolver
     */
    private $setResolver;
    public function __construct(SetResolver $setResolver)
    {
        $this->setResolver = $setResolver;
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $fileInfos
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function resolveFromFileInfos(array $fileInfos): array
    {
        $setFileInfos = [];
        foreach ($fileInfos as $fileInfo) {
            $setsNames = $this->resolveSetsFromFileInfo($fileInfo);
            foreach ($setsNames as $setsName) {
                $setFileInfos[] = $this->setResolver->detectFromName($setsName);
            }
        }
        return $setFileInfos;
    }
    /**
     * @return string[]
     */
    private function resolveSetsFromFileInfo(SmartFileInfo $configFileInfo): array
    {
        if ($configFileInfo->hasSuffixes(['yml', 'yaml'])) {
            return $this->resolveSetsParameterFromYamlFileInfo($configFileInfo);
        }
        return $this->resolveSetsParameterFromPhpFileInfo($configFileInfo);
    }
    /**
     * @return string[]
     */
    private function resolveSetsParameterFromYamlFileInfo(SmartFileInfo $configFileInfo): array
    {
        $configContent = Yaml::parse($configFileInfo->getContents());
        return (array) ($configContent['parameters'][self::SETS] ?? []);
    }
    /**
     * @return string[]
     */
    private function resolveSetsParameterFromPhpFileInfo(SmartFileInfo $configFileInfo): array
    {
        // php file loader
        $containerBuilder = new ContainerBuilder();
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator());
        $phpFileLoader->load($configFileInfo->getRealPath());
        if (!$containerBuilder->hasParameter(self::SETS)) {
            return [];
        }
        return (array) $containerBuilder->getParameter(self::SETS);
    }
}
