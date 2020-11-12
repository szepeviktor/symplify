<?php

declare(strict_types=1);
namespace Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\ValueObject;

use Migrify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Symplify\CodingStandard\ValueObject\NotImplementedYetException;
use Symplify\PackageBuilder\Neon\ValueObject\NeonPrinter;
use Symplify\RuleDocGenerator\Printer\ValueObject\MarkdownCodeWrapper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
final class ConfiguredCodeSamplePrinter
{
    /**
     * @var \Symplify\PackageBuilder\Neon\ValueObject\NeonPrinter
     */
    private $neonPrinter;
    /**
     * @var \Symplify\RuleDocGenerator\Printer\ValueObject\MarkdownCodeWrapper
     */
    private $markdownCodeWrapper;
    /**
     * @var SmartPhpConfigPrinter
     */
    private $smartPhpConfigPrinter;
    public function __construct(NeonPrinter $neonPrinter, MarkdownCodeWrapper $markdownCodeWrapper, \Migrify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter $smartPhpConfigPrinter)
    {
        $this->neonPrinter = $neonPrinter;
        $this->markdownCodeWrapper = $markdownCodeWrapper;
        $this->smartPhpConfigPrinter = $smartPhpConfigPrinter;
    }
    /**
     * @return mixed[]|string[]
     */
    public function print(ConfiguredCodeSample $configuredCodeSample, RuleDefinition $ruleDefinition): array
    {
        if ($ruleDefinition->isPHPStanRule()) {
            $lines = $this->printPHPStanConfiguration($ruleDefinition, $configuredCodeSample);
            $lines[] = '↓';
            return $lines;
        }
        if ($ruleDefinition->isPHPCSFixer()) {
            $lines = [];
            $configContent = $this->smartPhpConfigPrinter->printConfiguredServices([$ruleDefinition->getRuleClass() => $configuredCodeSample->getConfiguration()]);
            $lines[] = $this->markdownCodeWrapper->printPhpCode($configContent);
            $lines[] = '↓';
            return $lines;
        }
        // @todo configured sniff
        throw new NotImplementedYetException();
    }
    /**
     * @return mixed[]
     */
    private function printPHPStanConfiguration(RuleDefinition $ruleDefinition, ConfiguredCodeSample $configuredCodeSample): array
    {
        $lines = [];
        $phpstanNeon = ['services' => [['class' => $ruleDefinition->getRuleClass(), 'tags' => ['phpstan.rules.rule'], 'arguments' => $configuredCodeSample->getConfiguration()]]];
        $printedNeon = $this->neonPrinter->printNeon($phpstanNeon);
        $lines[] = $this->markdownCodeWrapper->printYamlCode($printedNeon);
        return $lines;
    }
}
