<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\Bootstrap\ValueObject;

use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class ConfigHasher
{
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $configFileInfos
     */
    public function computeFileInfosHash(array $configFileInfos): string
    {
        $hash = '';
        foreach ($configFileInfos as $config) {
            $hash .= md5_file($config->getRealPath());
        }
        return $hash;
    }
}
