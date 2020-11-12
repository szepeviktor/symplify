<?php

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\ChangelogLinker\HttpKernel\ValueObject\ChangelogLinkerKernel;
use Symplify\SetConfigResolver\ValueObject\ConfigResolver;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;

        break;
    }
}

$configFileInfos = [];

$configResolver = new ConfigResolver();
$inputConfigFileInfos = $configResolver->resolveFromInputWithFallback(new ArgvInput(), ['changelog-linker.php']);

if ($inputConfigFileInfos !== null) {
    $configFileInfos[] = $inputConfigFileInfos;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(ChangelogLinkerKernel::class, $configFileInfos);
$kernelBootAndApplicationRun->run();
