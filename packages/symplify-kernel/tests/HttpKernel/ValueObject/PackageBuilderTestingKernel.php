<?php

declare(strict_types=1);
namespace Symplify\SymplifyKernel\Tests\HttpKernel\ValueObject;

use Symplify\SymplifyKernel\Console\ValueObject\AbstractSymplifyConsoleApplication;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;
use Symplify\ChangelogLinker\Configuration\ValueObject\Option;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class PackageBuilderTestingKernel extends AbstractSymplifyKernel
{
}
