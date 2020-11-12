<?php

declare(strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\ValueObject;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;
final class TokenFinder
{
    /**
     * @param int|Token $position
     */
    public function getPreviousMeaningfulToken(Tokens $tokens, $position): Token
    {
        if (is_int($position)) {
            return $this->findPreviousTokenByPosition($tokens, $position);
        }
        return $this->findPreviousTokenByToken($tokens, $position);
    }
    private function findPreviousTokenByPosition(Tokens $tokens, int $position): Token
    {
        $previousPosition = $position - 1;
        if (!isset($tokens[$previousPosition])) {
            throw new ShouldNotHappenException();
        }
        $previousToken = $tokens[$previousPosition];
        if (!$previousToken instanceof Token) {
            throw new ShouldNotHappenException();
        }
        return $previousToken;
    }
    private function findPreviousTokenByToken(Tokens $tokens, Token $positionToken): Token
    {
        $position = $this->resolvePositionByToken($tokens, $positionToken);
        return $this->findPreviousTokenByPosition($tokens, $position - 1);
    }
    private function resolvePositionByToken(Tokens $tokens, Token $positionToken): int
    {
        foreach ($tokens as $position => $token) {
            if ($token === $positionToken) {
                return $position;
            }
        }
        throw new ShouldNotHappenException();
    }
}
