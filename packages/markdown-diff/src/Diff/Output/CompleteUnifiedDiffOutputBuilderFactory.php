<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff\Diff\Output;

use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor;

/**
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 1000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor
     */
    private $privatesAccessor;

    public function __construct(PrivatesAccessor $privatesAccessor)
    {
        $this->privatesAccessor = $privatesAccessor;
    }

    /**
     * @api
     */
    public function create(): UnifiedDiffOutputBuilder
    {
        $unifiedDiffOutputBuilder = new UnifiedDiffOutputBuilder('');

        $this->privatesAccessor->setPrivateProperty($unifiedDiffOutputBuilder, 'contextLines', 10000);

        return $unifiedDiffOutputBuilder;
    }
}
