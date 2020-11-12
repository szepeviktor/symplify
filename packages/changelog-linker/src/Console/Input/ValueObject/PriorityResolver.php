<?php

declare(strict_types=1);
namespace Symplify\ChangelogLinker\Console\Input\ValueObject;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\ChangelogLinker\Configuration\ValueObject\Option;
use Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor;
final class PriorityResolver
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor
     */
    private $privatesAccessor;
    public function __construct()
    {
        $this->privatesAccessor = new PrivatesAccessor();
    }
    /**
     * Detects the order in which "--in-packages" and "--in-categories" are both called.
     * The first has a priority.
     */
    public function resolveFromInput(InputInterface $input): ?string
    {
        $rawOptions = $this->privatesAccessor->getPrivateProperty($input, 'options');
        $requiredOptions = [Option::IN_PACKAGES, Option::IN_CATEGORIES];
        if (count(array_intersect($requiredOptions, array_keys($rawOptions))) !== count($requiredOptions)) {
            return null;
        }
        $names = array_keys($rawOptions);
        foreach ($names as $name) {
            if ($name === Option::IN_PACKAGES) {
                return 'packages';
            }
            return 'categories';
        }
        return null;
    }
}
