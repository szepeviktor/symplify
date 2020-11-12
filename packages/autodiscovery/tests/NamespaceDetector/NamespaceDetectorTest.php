<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests\NamespaceDetector;

use PHPUnit\Framework\TestCase;
use Symplify\Autodiscovery\ValueObject\NamespaceDetector;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

final class NamespaceDetectorTest extends TestCase
{
    public function test(): void
    {
        $namespaceDetector = new NamespaceDetector(new SmartFileSystem());
        $directoryFileInfo = new SmartFileInfo(__DIR__ . '/Source');

        $this->assertSame(
            'Symplify\Autodiscovery\Tests\NamespaceDetector\Source',
            $namespaceDetector->detectFromDirectory($directoryFileInfo)
        );
    }
}
