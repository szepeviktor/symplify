<?php

declare(strict_types=1);
namespace Symplify\RuleDocGenerator\Printer\ValueObject;

use Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\ValueObject\CodeSamplePrinter;
use Symplify\RuleDocGenerator\ValueObject\Lines;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
final class RuleDefinitionsPrinter
{
    /**
     * @var \Symplify\RuleDocGenerator\Printer\CodeSamplePrinter\ValueObject\CodeSamplePrinter
     */
    private $codeSamplePrinter;
    public function __construct(CodeSamplePrinter $codeSamplePrinter)
    {
        $this->codeSamplePrinter = $codeSamplePrinter;
    }
    /**
     * @param RuleDefinition[] $ruleDefinitions
     * @return string[]
     */
    public function print(array $ruleDefinitions): array
    {
        $lines = [];
        $lines[] = '# Rules Overview';
        foreach ($ruleDefinitions as $ruleDefinition) {
            $lines[] = '## ' . $ruleDefinition->getRuleShortClass();
            $lines[] = $ruleDefinition->getDescription();
            if ($ruleDefinition->isConfigurable()) {
                $lines[] = Lines::CONFIGURE_IT;
            }
            $lines[] = '- class: `' . $ruleDefinition->getRuleClass() . '`';
            $codeSampleLines = $this->codeSamplePrinter->print($ruleDefinition);
            $lines = array_merge($lines, $codeSampleLines);
        }
        return $lines;
    }
}
