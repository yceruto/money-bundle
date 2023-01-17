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

namespace Yceruto\MoneyBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Money\Currencies;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\MoneyFormatter;
use Money\MoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;
use Money\Parser\IntlMoneyParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Form\Forms;
use Twig\Environment;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\CurrenciesPass;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\FormattersPass;
use Yceruto\MoneyBundle\Formatter\IntlNumberFormatterFactory;

class MoneyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $container->setParameter('.money_currencies', $config['currencies']);
        $container->setParameter('.money_formatter_fraction_digits', $config['formatters']['bitcoin']['fraction_digits']);
        $container->setParameter('.money_formatter_number_locale', $config['formatters']['intl']['number_locale']);
        $container->setParameter('.money_formatter_number_style', $config['formatters']['intl']['number_style']);
        $container->setParameter('.money_formatter_number_pattern', $config['formatters']['intl']['number_pattern']);
        $container->setParameter('.money_exchange_fixed', $config['exchanges']['fixed']);

        $container->registerForAutoconfiguration(Currencies::class)
            ->addTag(CurrenciesPass::TAG);
        $container->registerForAutoconfiguration(MoneyFormatter::class)
            ->addTag(FormattersPass::TAG);
        $container->registerForAutoconfiguration(MoneyParser::class)
            ->addTag(FormattersPass::TAG);

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2).'/config'));
        $loader->load('currencies.php');
        $loader->load('formatters.php');
        $loader->load('parsers.php');
        $loader->load('exchangers.php');

        if (!class_exists(\NumberFormatter::class)) {
            $container->removeDefinition(IntlNumberFormatterFactory::class);
            $container->removeDefinition(IntlMoneyFormatter::class);
            $container->removeDefinition(IntlLocalizedDecimalFormatter::class);
            $container->removeDefinition(IntlMoneyParser::class);
            $container->removeDefinition(IntlLocalizedDecimalParser::class);
        }

        if (class_exists(Forms::class) && $config['form']['enabled']) {
            $loader->load('form.php');
        }

        if (class_exists(Environment::class) && $config['twig']['enabled']) {
            $loader->load('twig.php');
        }

        if (class_exists(DoctrineBundle::class) && $config['doctrine']['enabled']) {
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'mappings' => [
                        'Money' => [
                            'is_bundle' => false,
                            'type' => 'xml',
                            'dir' => \dirname(__DIR__, 2).'/config/doctrine/mapping/',
                            'prefix' => 'Money',
                        ],
                    ],
                ],
            ]);
        }
    }
}
