<?php

declare(strict_types=1);
namespace Symplify\SymfonyStaticDumper\ValueObject;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\ValueObject\ControllerWithDataProviderMatcher;
use Symplify\SymfonyStaticDumper\FileSystem\ValueObject\FilePathResolver;
use Symplify\SymfonyStaticDumper\HttpFoundation\ValueObject\ControllerContentResolver;
use Symplify\SymfonyStaticDumper\Routing\ValueObject\RoutesProvider;
final class ControllerDumper
{
    /**
     * @var \Symplify\SymfonyStaticDumper\ValueObject\ControllerWithDataProviderMatcher
     */
    private $controllerWithDataProviderMatcher;
    /**
     * @var \Symplify\SymfonyStaticDumper\HttpFoundation\ValueObject\ControllerContentResolver
     */
    private $controllerContentResolver;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\SymfonyStaticDumper\Routing\ValueObject\RoutesProvider
     */
    private $routesProvider;
    /**
     * @var \Symplify\SymfonyStaticDumper\FileSystem\ValueObject\FilePathResolver
     */
    private $filePathResolver;
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(ControllerWithDataProviderMatcher $controllerWithDataProviderMatcher, ControllerContentResolver $controllerContentResolver, RoutesProvider $routesProvider, \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, FilePathResolver $filePathResolver, SmartFileSystem $smartFileSystem)
    {
        $this->controllerWithDataProviderMatcher = $controllerWithDataProviderMatcher;
        $this->controllerContentResolver = $controllerContentResolver;
        $this->symfonyStyle = $symfonyStyle;
        $this->routesProvider = $routesProvider;
        $this->filePathResolver = $filePathResolver;
        $this->smartFileSystem = $smartFileSystem;
    }
    public function dump(string $outputDirectory): void
    {
        $this->dumpControllerWithoutParametersContents($outputDirectory);
        $this->dumpControllerWithParametersContents($outputDirectory);
    }
    private function dumpControllerWithoutParametersContents($outputDirectory): void
    {
        $routesWithoutArguments = $this->routesProvider->provideRoutesWithoutArguments();
        $progressBar = $this->createProgressBarIfNeeded($routesWithoutArguments);
        foreach ($routesWithoutArguments as $routeName => $route) {
            $fileContent = $this->controllerContentResolver->resolveFromRoute($routeName, $route);
            if ($fileContent === null) {
                continue;
            }
            $filePath = $this->filePathResolver->resolveFilePath($route, $outputDirectory);
            $this->printProgressOrDumperFileInfo($route, $filePath, $progressBar);
            $this->smartFileSystem->dumpFile($filePath, $fileContent);
        }
    }
    private function dumpControllerWithParametersContents(string $outputDirectory): void
    {
        $routesWithParameters = $this->routesProvider->provideRoutesWithParameters();
        foreach ($routesWithParameters as $routeName => $route) {
            $controllerWithDataProvider = $this->controllerWithDataProviderMatcher->matchRoute($route);
            if ($controllerWithDataProvider === null) {
                continue;
            }
            $this->printHeadline($controllerWithDataProvider, $routeName);
            $progressBar = $this->createProgressBarIfNeeded($controllerWithDataProvider->getArguments());
            $this->processControllerWithDataProvider($controllerWithDataProvider, $routeName, $route, $outputDirectory, $progressBar);
        }
    }
    private function createProgressBarIfNeeded(array $items): ?ProgressBar
    {
        if ($this->symfonyStyle->isDebug()) {
            // show file names on debug, no progress bar
            return null;
        }
        $stepCount = count($items);
        return $this->symfonyStyle->createProgressBar($stepCount);
    }
    private function printProgressOrDumperFileInfo(Route $route, string $filePath, ?ProgressBar $progressBar): void
    {
        if ($progressBar instanceof ProgressBar) {
            $progressBar->advance();
            return;
        }
        $message = sprintf('Dumping static content for "%s" route to "%s" path', $route->getPath(), $filePath);
        $this->symfonyStyle->note($message);
    }
    private function printHeadline(ControllerWithDataProviderInterface $controllerWithDataProvider, $routeName): void
    {
        $this->symfonyStyle->newLine(2);
        $message = sprintf('Dumping data for "%s" data provider and "%s" route', get_class($controllerWithDataProvider), $routeName);
        $this->symfonyStyle->section($message);
    }
    private function processControllerWithDataProvider(ControllerWithDataProviderInterface $controllerWithDataProvider, $routeName, $route, string $outputDirectory, ?ProgressBar $progressBar): void
    {
        $arguments = $controllerWithDataProvider->getArguments();
        foreach ($arguments as $argument) {
            $fileContent = $this->controllerContentResolver->resolveFromRouteAndArgument($routeName, $route, $argument);
            if ($fileContent === null) {
                continue;
            }
            $filePath = $this->filePathResolver->resolveFilePathWithArgument($route, $outputDirectory, $argument);
            $this->printProgressOrDumperFileInfo($route, $filePath, $progressBar);
            $this->smartFileSystem->dumpFile($filePath, $fileContent);
        }
    }
}
