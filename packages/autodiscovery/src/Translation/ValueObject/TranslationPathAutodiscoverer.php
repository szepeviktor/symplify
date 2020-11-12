<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\Translation\ValueObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\Finder\ValueObject\AutodiscoveryFinder;
/**
 * @see https://symfony.com/doc/current/translation.html#translation-resource-file-names-and-locations
 */
final class TranslationPathAutodiscoverer implements AutodiscovererInterface
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
        $this->containerBuilder = $containerBuilder;
        $this->autodiscoveryFinder = $autodiscoveryFinder;
    }
    public function autodiscover(): void
    {
        $paths = [];
        $translationDirectories = $this->autodiscoveryFinder->getTranslationDirectories();
        foreach ($translationDirectories as $translationDirectory) {
            $paths[] = $translationDirectory->getRealPath();
        }
        $this->containerBuilder->prependExtensionConfig('framework', ['translator' => ['paths' => $paths]]);
    }
}
