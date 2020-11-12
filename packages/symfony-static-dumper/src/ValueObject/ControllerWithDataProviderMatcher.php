<?php

declare(strict_types=1);
namespace Symplify\SymfonyStaticDumper\ValueObject;

use Symfony\Component\Routing\Route;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use Symplify\SymfonyStaticDumper\Routing\ValueObject\ControllerMatcher;
final class ControllerWithDataProviderMatcher
{
    /**
     * @var ControllerWithDataProviderInterface[]
     */
    private $controllerWithDataProviders = [];
    /**
     * @var \Symplify\SymfonyStaticDumper\Routing\ValueObject\ControllerMatcher
     */
    private $controllerMatcher;
    /**
     * @param ControllerWithDataProviderInterface[] $controllerWithDataProviders
     */
    public function __construct(ControllerMatcher $controllerMatcher, array $controllerWithDataProviders)
    {
        $this->controllerMatcher = $controllerMatcher;
        $this->controllerWithDataProviders = $controllerWithDataProviders;
    }
    public function matchRoute(Route $route): ?ControllerWithDataProviderInterface
    {
        $controllerCallable = $this->controllerMatcher->matchRouteToControllerAndMethod($route);
        foreach ($this->controllerWithDataProviders as $controllerWithDataProvider) {
            if ($controllerWithDataProvider->getControllerClass() !== $controllerCallable->getClass()) {
                continue;
            }
            if ($controllerWithDataProvider->getControllerMethod() !== $controllerCallable->getMethod()) {
                continue;
            }
            return $controllerWithDataProvider;
        }
        return null;
    }
}
