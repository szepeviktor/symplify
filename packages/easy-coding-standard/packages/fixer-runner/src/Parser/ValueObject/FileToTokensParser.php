<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Parser\ValueObject;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
final class FileToTokensParser
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }
    public function parseFromFilePath(string $filePath): Tokens
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Tokens::fromCode($fileContent);
    }
}
