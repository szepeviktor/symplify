<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger;

use Symplify\MonorepoBuilder\Merge\Arrays\ValueObject\ArraySorter;
use Symplify\PackageBuilder\Yaml\ValueObject\ParametersMerger;

abstract class AbstractComposerKeyMerger
{
    /**
     * @var \Symplify\PackageBuilder\Yaml\ValueObject\ParametersMerger
     */
    protected $parametersMerger;

    /**
     * @var \Symplify\MonorepoBuilder\Merge\Arrays\ValueObject\ArraySorter
     */
    protected $arraySorter;

    /**
     * @required
     */
    public function autowireAbstractComposerKeyMerger(
        ParametersMerger $parametersMerger,
        ArraySorter $arraySorter
    ): void {
        $this->parametersMerger = $parametersMerger;
        $this->arraySorter = $arraySorter;
    }

    /**
     * @return mixed[]
     */
    protected function mergeRecursiveAndSort(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->mergeWithCombine($firstArray, $secondArray);

        return $this->arraySorter->recursiveSort($mergedArray);
    }

    /**
     * @return mixed[]
     */
    protected function mergeAndSort(array $firstArray, array $secondArray): array
    {
        $mergedArray = $this->parametersMerger->merge($firstArray, $secondArray);

        return $this->arraySorter->recursiveSort($mergedArray);
    }
}
