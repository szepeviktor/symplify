<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\Twig\ValueObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder;
/**
 * @see https://github.com/Haehnchen/idea-php-symfony2-plugin/issues/1216
 */
final class TwigPathAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;
    /**
     * @var \Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder
     */
    private $autodiscoveryFinder;
    public function __construct(\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, AutodiscoveryFinder $autodiscoveryFinder)
    {
        $this->autodiscoveryFinder = $autodiscoveryFinder;
        $this->containerBuilder = $containerBuilder;
    }
    public function autodiscover(): void
    {
        $paths = [];
        $templatesDirectories = $this->autodiscoveryFinder->getTemplatesDirectories();
        foreach ($templatesDirectories as $templatesDirectory) {
            $paths[] = $templatesDirectory->getRealPath();
        }
        $this->containerBuilder->prependExtensionConfig('twig', ['paths' => $paths]);
    }
}
