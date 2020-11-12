<?php

declare(strict_types=1);
namespace Symplify\SetConfigResolver\ValueObject;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\SetConfigResolver\Config\ValueObject\SetsParameterResolver;
use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
/**
 * @see \Symplify\SetConfigResolver\Tests\ConfigResolver\SetAwareConfigResolverTest
 */
final class SetAwareConfigResolver extends AbstractConfigResolver
{
    /**
     * @var \Symplify\SetConfigResolver\Config\ValueObject\SetsParameterResolver
     */
    private $setsParameterResolver;
    /**
     * @var \Symplify\SetConfigResolver\ValueObject\SetResolver
     */
    private $setResolver;
    public function __construct(SetProviderInterface $setProvider)
    {
        $this->setResolver = new SetResolver($setProvider);
        $this->setsParameterResolver = new SetsParameterResolver($this->setResolver);
        parent::__construct();
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $fileInfos
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function resolveFromParameterSetsFromConfigFiles(array $fileInfos): array
    {
        return $this->setsParameterResolver->resolveFromFileInfos($fileInfos);
    }
    public function resolveSetFromInput(InputInterface $input): ?SmartFileInfo
    {
        return $this->setResolver->detectFromInput($input);
    }
}
