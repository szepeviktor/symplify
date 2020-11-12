<?php

declare(strict_types=1);
namespace Symplify\FlexLoader\Flex\ValueObject;

use Iterator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\FlexLoader\ValueObject\ConfigurationException;
final class FlexLoader
{
    /**
     * @var string
     */
    private const CONFIG_EXTENSIONS = '.{php,xml,yaml,yml}';
    /**
     * @var string
     */
    private $environment;
    /**
     * @var string
     */
    private $projectDir;
    /**
     * @var FlexPathsFactory
     */
    private $flexPathsFactory;
    public function __construct(string $environment, string $projectDir)
    {
        $this->ensureArgumentsAreNotSwapped($environment, $projectDir);
        $this->environment = $environment;
        $this->projectDir = $projectDir;
        $this->flexPathsFactory = new \Symplify\FlexLoader\Flex\FlexPathsFactory();
    }
    /**
     * @param string[] $extraServicePaths
     */
    public function loadConfigs(ContainerBuilder $containerBuilder, LoaderInterface $loader, array $extraServicePaths = []): void
    {
        if (file_exists($this->getBundlesFilePath())) {
            $containerBuilder->addResource(new FileResource($this->getBundlesFilePath()));
            $containerBuilder->setParameter('container.dumper.inline_class_loader', true);
        }
        $servicePaths = $this->flexPathsFactory->createServicePaths($this->projectDir, $this->environment, $extraServicePaths);
        foreach ($servicePaths as $servicePath) {
            $loader->load($servicePath . self::CONFIG_EXTENSIONS, 'glob');
        }
    }
    /**
     * @param string[] $extraRoutingPaths
     */
    public function loadRoutes(RouteCollectionBuilder $routeCollectionBuilder, array $extraRoutingPaths = []): void
    {
        $routingPaths = $this->flexPathsFactory->createRoutingPaths($this->projectDir, $this->environment);
        $routingPaths = array_merge($extraRoutingPaths, $routingPaths);
        foreach ($routingPaths as $routingPath) {
            $routeCollectionBuilder->import($routingPath . self::CONFIG_EXTENSIONS, '/', 'glob');
        }
    }
    public function loadBundles(): Iterator
    {
        $contents = (require $this->getBundlesFilePath());
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                (yield new $class());
            }
        }
    }
    private function ensureArgumentsAreNotSwapped(string $environment, string $projectDir): void
    {
        if (file_exists($projectDir)) {
            return;
        }
        throw new ConfigurationException(sprintf('Switch "%s" ($environment) and "%s" ($projectDir) in "new %s(...)".', $projectDir, $environment, self::class));
    }
    private function getBundlesFilePath(): string
    {
        return $this->projectDir . '/config/bundles.php';
    }
}
