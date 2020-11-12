<?php

declare(strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\ValueObject;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\CallAnalyzer;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenNotFoundException;
final class LineLengthOpenerTransformer
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ValueObject\CallAnalyzer
     */
    private $callAnalyzer;
    public function __construct(CallAnalyzer $callAnalyzer)
    {
        $this->callAnalyzer = $callAnalyzer;
    }
    public function insertNewlineAfterOpeningIfNeeded(Tokens $tokens, int $blockStartIndex, string $newlineIndentWhitespace): void
    {
        if (!isset($tokens[$blockStartIndex + 1])) {
            throw new TokenNotFoundException($blockStartIndex + 1);
        }
        /** @var Token $nextToken */
        $nextToken = $tokens[$blockStartIndex + 1];
        if ($nextToken->isGivenKind(T_WHITESPACE)) {
            $tokens->ensureWhitespaceAtIndex($blockStartIndex + 1, 0, $newlineIndentWhitespace);
            return;
        }
        // special case, if the function is followed by array - method([...])
        if ($nextToken->isGivenKind([T_ARRAY, CT::T_ARRAY_SQUARE_BRACE_OPEN]) && !$this->callAnalyzer->isMethodCall($tokens, $blockStartIndex)) {
            $tokens->ensureWhitespaceAtIndex($blockStartIndex + 1, 1, $newlineIndentWhitespace);
            return;
        }
        $tokens->ensureWhitespaceAtIndex($blockStartIndex, 1, $newlineIndentWhitespace);
    }
}
