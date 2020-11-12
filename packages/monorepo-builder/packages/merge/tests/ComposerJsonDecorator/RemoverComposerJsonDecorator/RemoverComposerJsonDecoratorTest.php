<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\RemoverComposerJsonDecorator;

use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\HttpKernel\ValueObject\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\ValueObject\RemoverComposerJsonDecorator;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;

final class RemoverComposerJsonDecoratorTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var mixed[]
     */
    private const COMPOSER_JSON_DATA = [
        'require' => [
            'phpunit/phpunit' => 'v1.0.0',
            'rector/rector' => 'v1.0.0',
        ],
        'autoload-dev' => [
            'psr-4' => [
                'Symplify\Tests\\' => 'tests',
                'Symplify\SuperTests\\' => 'super-tests',
            ],
            'files' => ['src/SomeFile.php', 'src/KeepFile.php'],
        ],
    ];

    /**
     * @var ComposerJson
     */
    private $composerJson;

    /**
     * @var ComposerJson
     */
    private $expectedComposerJson;

    /**
     * @var \Symplify\MonorepoBuilder\Merge\ComposerJsonDecorator\ValueObject\RemoverComposerJsonDecorator
     */
    private $removerComposerJsonDecorator;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        /** @var \Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider $parameterProvider */
        $parameterProvider = self::$container->get(ParameterProvider::class);
        $parameterProvider->changeParameter(Option::DATA_TO_REMOVE, [
            'require' => [
                'phpunit/phpunit' => '*',
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Symplify\\Tests\\' => 'tests',
                ],
                'files' => ['src/SomeFile.php'],
            ],
        ]);

        $this->removerComposerJsonDecorator = self::$container->get(RemoverComposerJsonDecorator::class);

        $this->composerJson = $this->createMainComposerJson();
        $this->expectedComposerJson = $this->createExpectedComposerJson();
    }

    public function test(): void
    {
        $this->removerComposerJsonDecorator->decorate($this->composerJson);

        $this->assertComposerJsonEquals($this->expectedComposerJson, $this->composerJson);
    }

    private function createMainComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = self::$container->get(ComposerJsonFactory::class);

        return $composerJsonFactory->createFromArray(self::COMPOSER_JSON_DATA);
    }

    private function createExpectedComposerJson(): ComposerJson
    {
        /** @var ComposerJsonFactory $composerJsonFactory */
        $composerJsonFactory = self::$container->get(ComposerJsonFactory::class);

        $expectedComposerJson = [
            'require' => [
                'rector/rector' => 'v1.0.0',
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Symplify\SuperTests\\' => 'super-tests',
                ],
                'files' => [
                    1 => 'src/KeepFile.php',
                ],
            ],
        ];

        return $composerJsonFactory->createFromArray($expectedComposerJson);
    }
}
