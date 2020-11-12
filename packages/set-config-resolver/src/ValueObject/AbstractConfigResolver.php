<?php

declare(strict_types=1);
namespace Symplify\SetConfigResolver\ValueObject;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\SetConfigResolver\Console\Option\ValueObject\OptionName;
use Symplify\SetConfigResolver\Console\ValueObject\OptionValueResolver;
use Symplify\SmartFileSystem\ValueObject\FileNotFoundException;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
abstract class AbstractConfigResolver
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileInfo|null
     */
    private $firstResolvedConfigFileInfo;
    /**
     * @var \Symplify\SetConfigResolver\Console\ValueObject\OptionValueResolver
     */
    private $optionValueResolver;
    public function __construct()
    {
        $this->optionValueResolver = new OptionValueResolver();
    }
    public function resolveFromInput(InputInterface $input): ?SmartFileInfo
    {
        $configValue = $this->optionValueResolver->getOptionValue($input, OptionName::CONFIG);
        if ($configValue !== null) {
            if (!file_exists($configValue)) {
                $message = sprintf('File "%s" was not found', $configValue);
                throw new FileNotFoundException($message);
            }
            return $this->createFileInfo($configValue);
        }
        return null;
    }
    /**
     * @param string[] $fallbackFiles
     */
    public function resolveFromInputWithFallback(InputInterface $input, array $fallbackFiles): ?SmartFileInfo
    {
        $configFileInfo = $this->resolveFromInput($input);
        if ($configFileInfo !== null) {
            return $configFileInfo;
        }
        return $this->createFallbackFileInfoIfFound($fallbackFiles);
    }
    public function getFirstResolvedConfigFileInfo(): ?SmartFileInfo
    {
        return $this->firstResolvedConfigFileInfo;
    }
    /**
     * @param string[] $fallbackFiles
     */
    private function createFallbackFileInfoIfFound(array $fallbackFiles): ?SmartFileInfo
    {
        foreach ($fallbackFiles as $fallbackFile) {
            $rootFallbackFile = getcwd() . DIRECTORY_SEPARATOR . $fallbackFile;
            if (is_file($rootFallbackFile)) {
                return $this->createFileInfo($rootFallbackFile);
            }
        }
        return null;
    }
    private function createFileInfo(string $configValue): SmartFileInfo
    {
        $configFileInfo = new SmartFileInfo($configValue);
        $this->firstResolvedConfigFileInfo = $configFileInfo;
        return $configFileInfo;
    }
}
