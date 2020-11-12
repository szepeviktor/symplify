<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\ArrayWrapper;

final class ArrayWrapperFactory
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(TokenSkipper $tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }

    public function createFromTokensAndBlockInfo(Tokens $tokens, BlockInfo $blockInfo): ArrayWrapper
    {
        return new ArrayWrapper($tokens, $blockInfo, $this->tokenSkipper);
    }
}
