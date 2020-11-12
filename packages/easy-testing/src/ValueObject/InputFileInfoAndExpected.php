<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class InputFileInfoAndExpected
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileInfo
     */
    private $inputFileInfo;

    /**
     * @var mixed
     */
    private $expected;

    /**
     * @param mixed $expected
     */
    public function __construct(SmartFileInfo $inputFileInfo, $expected)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expected = $expected;
    }

    public function getInputFileInfo(): SmartFileInfo
    {
        return $this->inputFileInfo;
    }

    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }
}
