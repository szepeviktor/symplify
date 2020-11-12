<?php

declare(strict_types=1);

use Symplify\RuleDocGenerator\HttpKernel\ValueObject\RuleDocGeneratorKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

# 1. autoload
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

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(RuleDocGeneratorKernel::class);
$kernelBootAndApplicationRun->run();
