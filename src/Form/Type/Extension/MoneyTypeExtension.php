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

namespace Yceruto\MoneyBundle\Form\Type\Extension;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly Currencies $currencies = new ISOCurrencies(),
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'scale' => function (Options $options) {
                return $this->currencies->subunitFor(new Currency($options['currency']));
            },
            'divisor' => function (Options $options) {
                return 0 === $options['scale'] ? 1 : 10 ** $options['scale'];
            },
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        yield MoneyType::class;
    }
}
