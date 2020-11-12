<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Json\ValueObject;

use Symplify\MonorepoBuilder\Package\ValueObject\PackageProvider;
final class PackageJsonProvider
{
    /**
     * @var \Symplify\MonorepoBuilder\Package\ValueObject\PackageProvider
     */
    private $packageProvider;
    public function __construct(PackageProvider $packageProvider)
    {
        $this->packageProvider = $packageProvider;
    }
    /**
     * @return array<string, string[]>
     */
    public function createPackagePaths(): array
    {
        $packageRelativePaths = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageRelativePaths[] = $package->getRelativePath();
        }
        return ['package_path' => $packageRelativePaths];
    }
    /**
     * @return array<string, string[]>
     */
    public function createPackageNames(): array
    {
        $packageShortNames = [];
        foreach ($this->packageProvider->provide() as $package) {
            $packageShortNames[] = $package->getShortName();
        }
        return ['package_name' => $packageShortNames];
    }
}
