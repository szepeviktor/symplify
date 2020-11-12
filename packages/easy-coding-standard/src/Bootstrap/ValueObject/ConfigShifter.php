<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\Bootstrap\ValueObject;

use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class ConfigShifter
{
    /**
     * Shift input config as last, so the parameters override previous rules loaded from sets
     *
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $configFileInfos
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] <int, SmartFileInfo>
     */
    public function shiftInputConfigAsLast(array $configFileInfos, ?SmartFileInfo $inputConfigFileInfo): array
    {
        if ($inputConfigFileInfo === null) {
            return $configFileInfos;
        }
        $mainConfigShiftedAsLast = [];
        foreach ($configFileInfos as $configFileInfo) {
            if ($configFileInfo !== $inputConfigFileInfo) {
                $mainConfigShiftedAsLast[] = $configFileInfo;
            }
        }
        $mainConfigShiftedAsLast[] = $inputConfigFileInfo;
        return $mainConfigShiftedAsLast;
    }
}
