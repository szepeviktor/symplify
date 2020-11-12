<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer;

use Symplify\CodingStandard\Fixer\ValueObject\AbstractSymplifyFixer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Contract\ArrayFixerInterface;
use Symplify\CodingStandard\Fixer\LineLength\ValueObject\LineLengthFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\ArrayAnalyzer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\BlockFinder;

abstract class AbstractArrayFixer extends AbstractSymplifyFixer implements ArrayFixerInterface
{
    /**
     * @var int[]
     */
    protected const ARRAY_OPEN_TOKENS = [T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN];

    /**
     * @var WhitespacesFixerConfig
     */
    protected $whitespacesFixerConfig;

    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\ArrayAnalyzer
     */
    protected $arrayAnalyzer;

    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\BlockFinder
     */
    private $blockFinder;

    /**
     * @required
     */
    public function autowireAbstractArrayFixer(
        BlockFinder $blockFinder,
        WhitespacesFixerConfig $whitespacesFixerConfig,
        ArrayAnalyzer $arrayAnalyzer
    ): void {
        $this->blockFinder = $blockFinder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->arrayAnalyzer = $arrayAnalyzer;
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::ARRAY_OPEN_TOKENS)
            && $tokens->isTokenKindFound(T_DOUBLE_ARROW);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $reverseTokens = $this->reverseTokens($tokens);
        foreach ($reverseTokens as $index => $token) {
            if (! $token->isGivenKind(self::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if ($blockInfo === null) {
                continue;
            }

            $this->fixArrayOpener($tokens, $blockInfo, $index);
        }
    }

    public function getPriority(): int
    {
        // to handle the indent
        return $this->getPriorityBefore(LineLengthFixer::class);
    }
}
