<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\RemoveCommentedCodeFixerTest
 */
final class RemoveCommentedCodeFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove commented code like "// $one = 1000;" comment';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $tokens = token_get_all('<?php '. file_get_contents($file->getRealPath()) .' ?>');

        foreach ($tokens as $token) {
            if (isset(Tokens::$emptyTokens[$token]['code']) === false) {
                break;
            }

            if ($token['code'] === T_WHITESPACE) {
                continue;
            }

            if (isset(Tokens::$phpcsCommentTokens[$token['code']]) === true) {
                $lastLineSeen = $token['line'];
                continue;
            }

            if ($commentStyle === 'line'
                && ($lastLineSeen + 1) <= $token['line']
                && strpos($token['content'], '/*') === 0
            ) {
                // First non-whitespace token on a new line is start of a different style comment.
                break;
            }

            if ($commentStyle === 'line'
                && ($lastLineSeen + 1) < $token['line']
            ) {
                // Blank line breaks a '//' style comment block.
                break;
            }

            /*
                Trim as much off the comment as possible so we don't
                have additional whitespace tokens or comment tokens
            */

            $tokenContent = trim($token['content']);
            $break        = false;

            if ($commentStyle === 'line') {
                if (substr($tokenContent, 0, 2) === '//') {
                    $tokenContent = substr($tokenContent, 2);
                }

                if (substr($tokenContent, 0, 1) === '#') {
                    $tokenContent = substr($tokenContent, 1);
                }
            } else {
                if (substr($tokenContent, 0, 3) === '/**') {
                    $tokenContent = substr($tokenContent, 3);
                }

                if (substr($tokenContent, 0, 2) === '/*') {
                    $tokenContent = substr($tokenContent, 2);
                }

                if (substr($tokenContent, -2) === '*/') {
                    $tokenContent = substr($tokenContent, 0, -2);
                    $break        = true;
                }

                if (substr($tokenContent, 0, 1) === '*') {
                    $tokenContent = substr($tokenContent, 1);
                }
            }//end if

            $content     .= $tokenContent.$phpcsFile->eolChar;
            $lastLineSeen = $token['line'];

            $lastCommentBlockToken = $i;

            if ($break === true) {
                // Closer of a block comment found.
                break;
            }
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// $one = 1;
// $two = 2;
// $three = 3;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// note
CODE_SAMPLE
            ),
        ]);
    }
}
