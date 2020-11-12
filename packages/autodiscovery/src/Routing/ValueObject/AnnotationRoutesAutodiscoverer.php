<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\Routing\ValueObject;

use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder;
/**
 * @see \Symplify\Autodiscovery\Tests\Routing\AnnotationRoutesAutodiscovererTest
 */
final class AnnotationRoutesAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var RouteCollectionBuilder
     */
    private $routeCollectionBuilder;
    /**
     * @var \Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder
     */
    private $autodiscoveryFinder;
    public function __construct(\Symfony\Component\Routing\RouteCollectionBuilder $routeCollectionBuilder, AutodiscoveryFinder $autodiscoveryFinder)
    {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
        $this->autodiscoveryFinder = $autodiscoveryFinder;
    }
    public function autodiscover(): void
    {
        $controllerDirectories = $this->autodiscoveryFinder->getControllerDirectories();
        foreach ($controllerDirectories as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }
}
