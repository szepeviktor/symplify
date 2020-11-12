<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\ValueObject;

use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class Set
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileInfo
     */
    private $setFileInfo;

    public function __construct(string $name, SmartFileInfo $setFileInfo)
    {
        $this->name = $name;
        $this->setFileInfo = $setFileInfo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSetFileInfo(): SmartFileInfo
    {
        return $this->setFileInfo;
    }
}
