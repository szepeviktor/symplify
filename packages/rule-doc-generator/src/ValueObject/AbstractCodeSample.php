<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\ValueObject;

use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;

abstract class AbstractCodeSample implements CodeSampleInterface
{
    /**
     * @var string
     */
    private $goodCode;

    /**
     * @var string
     */
    private $badCode;

    public function __construct(string $goodCode, string $badCode)
    {
        if ($goodCode === '') {
            throw new ShouldNotHappenException('Code sample good code cannot be empty');
        }

        if ($goodCode === $badCode) {
            $errorMessage = sprintf('Good and bad code cannot be identical: "%s"', $goodCode);
            throw new ShouldNotHappenException($errorMessage);
        }

        $this->goodCode = $goodCode;
        $this->badCode = $badCode;
    }

    public function getGoodCode(): string
    {
        return $this->goodCode;
    }

    public function getBadCode(): string
    {
        return $this->badCode;
    }
}
