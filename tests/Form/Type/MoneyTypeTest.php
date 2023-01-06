<?php

declare(strict_types=1);

/*
 * This file is part of the MoneyBundle package.
 *
 * (c) Yonel Ceruto Gonzalez <yonelceruto@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yceruto\MoneyBundle\Tests\Form\Type;

use Money\Currencies\ISOCurrencies;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Yceruto\MoneyBundle\Form\Type\Extension\MoneyTypeExtension;

class MoneyTypeTest extends TestCase
{
    private OptionsResolver $resolver;
    private FormRenderer $formRenderer;

    protected function setUp(): void
    {
        $this->resolver = new OptionsResolver();

        $type = new MoneyType();
        $typeExtension = new MoneyTypeExtension(new ISOCurrencies());

        $type->configureOptions($this->resolver);
        $typeExtension->configureOptions($this->resolver);

        // -----------------------------------------------

        $loader = new ChainLoader([
            new FilesystemLoader(
                __DIR__.'/../../../vendor/symfony/twig-bridge/Resources/views/Form',
            ),
            new ArrayLoader([
                'money.twig' => '{{ form_widget(form) }}',
            ]),
        ]);
        $twig = new Environment($loader, ['strict_variables' => true]);
        $twig->addExtension(new TranslationExtension());
        $twig->addExtension(new FormExtension());

        $this->formRenderer = new FormRenderer(new TwigRendererEngine(['form_div_layout.html.twig'], $twig));

        $runtimeLoader = $this->createMock(RuntimeLoaderInterface::class);
        $runtimeLoader->method('load')->willReturn($this->returnValueMap([[FormRenderer::class, $this->formRenderer]]));
        $twig->addRuntimeLoader($runtimeLoader);
    }

    /**
     * @dataProvider moneyOptionsProvider
     */
    public function testDeriveScaleDivisorOptionsFromCurrency(array $options, string $currency, int $scale, int $divisor): void
    {
        $options = $this->resolver->resolve($options);

        self::assertSame($currency, $options['currency']);
        self::assertSame($scale, $options['scale']);
        self::assertSame($divisor, $options['divisor']);
    }

    /**
     * @return iterable<string, array{options: array, currency: string, scale: int, divisor: int}>
     */
    public function moneyOptionsProvider(): iterable
    {
        yield 'XOF' => [
            'options' => ['currency' => 'XOF'],
            'currency' => 'XOF',
            'scale' => 0,
            'divisor' => 1,
        ];

        yield 'EUR' => [
            'options' => ['currency' => 'EUR'],
            'currency' => 'EUR',
            'scale' => 2,
            'divisor' => 100,
        ];

        yield 'BHD' => [
            'options' => ['currency' => 'BHD'],
            'currency' => 'BHD',
            'scale' => 3,
            'divisor' => 1000,
        ];

        yield 'CLF' => [
            'options' => ['currency' => 'CLF'],
            'currency' => 'CLF',
            'scale' => 4,
            'divisor' => 10000,
        ];
    }

    /**
     * @dataProvider moneyRenderingProvider
     */
    public function testRendering(string $amount, string $currency, string $output): void
    {
        $view = Forms::createFormFactoryBuilder()
            ->addTypeExtension(new MoneyTypeExtension())
            ->getFormFactory()
            ->createNamed('money', MoneyType::class, $amount, ['currency' => $currency])
            ->createView();

        self::assertSame($output, $this->formRenderer->searchAndRenderBlock($view, 'widget'));
    }

    /**
     * @return iterable<string, array{amount: string, currency: string, output: string}>
     */
    public function moneyRenderingProvider(): iterable
    {
        yield 'USD' => [
            'amount' => '1230',
            'currency' => 'USD',
            'output' => '$ <input type="text" id="money" name="money" required="required" value="12.30" />',
        ];

        yield 'XOF' => [
            'amount' => '1230',
            'currency' => 'XOF',
            'output' => '<input type="text" id="money" name="money" required="required" value="1230" />',
        ];

        yield 'BHD' => [
            'amount' => '1230',
            'currency' => 'BHD',
            'output' => 'BHD <input type="text" id="money" name="money" required="required" value="1.230" />',
        ];
    }
}
