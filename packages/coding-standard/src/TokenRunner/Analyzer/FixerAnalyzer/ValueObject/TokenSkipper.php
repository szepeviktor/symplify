<?php

declare(strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenNotFoundException;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;
final class TokenSkipper
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\BlockFinder
     */
    private $blockFinder;
    public function __construct(BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }
    public function skipBlocks(Tokens $tokens, int $position): int
    {
        if (!isset($tokens[$position])) {
            throw new TokenNotFoundException($position);
        }
        $token = $tokens[$position];
        if ($token->getContent() === '{') {
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
            if ($blockInfo === null) {
                return $position;
            }
            return $blockInfo->getEnd();
        }
        if ($token->isGivenKind([CT::T_ARRAY_SQUARE_BRACE_OPEN, T_ARRAY])) {
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
            if ($blockInfo === null) {
                return $position;
            }
            return $blockInfo->getEnd();
        }
        return $position;
    }
    public function skipBlocksReversed(Tokens $tokens, int $position): int
    {
        /** @var Token $token */
        $token = $tokens[$position];
        if (!$token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE) && !$token->equals(')')) {
            return $position;
        }
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if ($blockInfo === null) {
            throw new ShouldNotHappenException();
        }
        return $blockInfo->getStart();
    }
}
