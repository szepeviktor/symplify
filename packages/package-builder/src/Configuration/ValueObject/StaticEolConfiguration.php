<?php

declare(strict_types=1);
namespace Symplify\PackageBuilder\Configuration\ValueObject;

final class StaticEolConfiguration
{
    public static function getEolChar(): string
    {
        return "\n";
    }
}
