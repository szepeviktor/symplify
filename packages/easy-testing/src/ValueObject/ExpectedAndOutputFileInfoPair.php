<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;

final class ExpectedAndOutputFileInfoPair
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileInfo
     */
    private $expectedFileInfo;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileInfo|null
     */
    private $outputFileInfo;

    public function __construct(SmartFileInfo $expectedFileInfo, ?SmartFileInfo $outputFileInfo)
    {
        $this->expectedFileInfo = $expectedFileInfo;
        $this->outputFileInfo = $outputFileInfo;
    }

    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getExpectedFileContent(): string
    {
        return $this->expectedFileInfo->getContents();
    }

    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function getOutputFileContent(): string
    {
        if ($this->outputFileInfo === null) {
            throw new ShouldNotHappenException();
        }

        return $this->outputFileInfo->getContents();
    }

    /**
     * @noRector \Rector\Privatization\Rector\ClassMethod\PrivatizeLocalOnlyMethodRector
     */
    public function doesOutputFileExist(): bool
    {
        return $this->outputFileInfo !== null;
    }
}
