<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\RandomApiMigrationFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Strict\ValueObject\BlankLineAfterStrictTypesFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);

    $services->set(RandomApiMigrationFixer::class)
        ->call('configure', [[
            'mt_rand' => 'random_int',
            'rand' => 'random_int',
        ]]);

    $services->set(TernaryToNullCoalescingFixer::class);

    $services->set(DeclareStrictTypesFixer::class);

    $services->set(BlankLineAfterStrictTypesFixer::class);

    $services->set(DeclareEqualNormalizeFixer::class);

    $services->set(ReturnTypeDeclarationFixer::class);

    $services->set(ReferenceThrowableOnlySniff::class);
};
