<?php

declare(strict_types=1);
namespace Symplify\SymplifyKernel\Strings\ValueObject;

use Nette\Utils\Strings;
use Symplify\SymplifyKernel\ValueObject\HttpKernel\TooGenericKernelClassException;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class KernelUniqueHasher
{
    /**
     * @var \Symplify\SymplifyKernel\Strings\ValueObject\StringsConverter
     */
    private $stringsConverter;
    public function __construct()
    {
        $this->stringsConverter = new StringsConverter();
    }
    public function hashKernelClass(string $kernelClass): string
    {
        $this->ensureIsNotGenericKernelClass($kernelClass);
        $shortClassName = (string) Strings::after($kernelClass, '\\', -1);
        return $this->stringsConverter->camelCaseToGlue($shortClassName, '_');
    }
    private function ensureIsNotGenericKernelClass(string $kernelClass): void
    {
        if ($kernelClass !== AbstractSymplifyKernel::class) {
            return;
        }
        $message = sprintf('Instead of "%s", provide final Kernel class', AbstractSymplifyKernel::class);
        throw new TooGenericKernelClassException($message);
    }
}
