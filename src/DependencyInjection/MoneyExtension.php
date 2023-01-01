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

use Money\Currencies;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Yceruto\MoneyBundle\DependencyInjection\Compiler\CurrenciesPass;

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

        $container->registerForAutoconfiguration(Currencies::class)
            ->addTag(CurrenciesPass::TAG);

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2).'/config'));
        $loader->load('currencies.php');
        $loader->load('formatters.php');

        if (!extension_loaded('intl')) {
            $container->removeDefinition(IntlMoneyFormatter::class);
            $container->removeDefinition(IntlLocalizedDecimalFormatter::class);
        }
    }
}
