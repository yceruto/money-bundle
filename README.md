# MoneyBundle

Symfony integration of the https://github.com/moneyphp/money library. For more information on how the MoneyPHP library works, 
please refer to its official documentation https://www.moneyphp.org.

<p>
    <a href="https://github.com/yceruto/money-bundle/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/yceruto/money-bundle/ci.yml?branch=main&label=tests&style=round-square"></a>
    <a href="https://packagist.org/packages/yceruto/money-bundle"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/yceruto/money-bundle"></a>
    <a href="https://packagist.org/packages/yceruto/money-bundle"><img alt="Latest Version" src="https://img.shields.io/packagist/v/yceruto/money-bundle"></a>
    <a href="https://packagist.org/packages/yceruto/money-bundle"><img alt="License" src="https://img.shields.io/github/license/yceruto/money-bundle"></a>
</p>

## Table of Contents
1. [Install](#install)
2. [Currencies](#currencies)
3. [Formatting](#formatting)
4. [Parsing](#parsing)
5. [Currency Conversion](#currency-conversion)
6. [Data Transfer Object](#data-transfer-object)
7. [Other Integrations](#other-integrations)
   * [Form](#form)
   * [Twig](#twig)
   * [Doctrine](#doctrine)
8. [License](#license)

## Install

This bundle is compatible with PHP 8.1 and above, as well as Symfony versions 5.4 and later.

```shell
 $ composer require yceruto/money-bundle
```

If you are not using `symfony/flex`, make sure to add the bundle to the `config/bundles.php` file. This will ensure that it 
is correctly registered and can be used in your application.

## Currencies

Applications often require a specific subset of currencies from different data sources. To facilitate this, you can 
implement the `Money\Currencies` interface, which provides a list of available currencies and the subunit for each currency.

The following currencies classes are available as services:

 * Money\Currencies\Currencies (alias for AggregateCurrencies)
 * Money\Currencies\CurrencyList
 * Money\Currencies\ISOCurrencies
 * Money\Currencies\BitcoinCurrencies
 * Money\Currencies\CryptoCurrencies

The `Currencies` interface is an alias for the `Money\Currencies\AggregateCurrencies` service, which comes with default
currency providers.

The providers are injected into the `AggregateCurrencies` service in the specified order, and you can add more providers 
by implementing the `Money\Currencies` interface and tagging it with `money.currencies` as a service.

The `Money\Currencies\CurrencyList` provider retrieves the currencies from the money configuration:

```yaml
 money:
     currencies:
         FOO: 2
```

The list consists of pairs of currency codes (strings) and subunits (integers). You can also use this configuration to 
override the subunit for `Money\Currencies\ISOCurrencies`.

In many cases, you may not know the exact currency that you will be formatting or parsing. For these scenarios, we have 
provided an aggregate formatter and parser service that allows you to configure multiple formatters/parsers and then 
choose the most appropriate one based on the value. You can find more information about this in the Formatting and 
Parsing section.

## Formatting

Money formatters can be helpful when you need to display a monetary value in a specific format. They allow you to convert a 
money object into a human-readable string, making it easier to present financial data to users. By using formatters, you 
can ensure that the money values you display are clear and easy to understand.

The following formatter classes are available as services:

 * Money\Formatter\MoneyFormatter (alias for AggregateMoneyFormatter)
 * Money\Formatter\IntMoneyFormatter (default if Intl extension is enabled)
 * Money\Formatter\IntLocalizedMoneyFormatter (available if Intl is enabled)
 * Money\Formatter\DecimalMoneyFormatter (default if Intl extension is disabled)
 * Money\Formatter\BitcoinMoneyFormatter (available for `XBT` currency code)

You can use the `Money\MoneyFormatter` interface as a dependency for any service because it is an alias for the `Money\Formatter\AggregateMoneyFormatter`
service, and it comes with default formatters.

Use the following configuration to set default values for the current formatters:

```yaml
 money:
     formatters:
         intl:
             number_locale: 'en_US'
             number_style: 2 # \NumberFormatter::CURRENCY
             number_pattern: null
         bitcoin:
             fraction_digits: 8
```

During a normal Symfony request, the money formatter will consider the current request locale when formatting the money object. 
This ensures that the formatted output is localized and suitable for the user's location.

To register a custom formatter, you will need to implement the `Money\MoneyFormatter` interface and tag the service with 
`money.formatter` and the currency `code` attribute that the formatter supports. This will allow you to use your custom 
formatter to format monetary values in a specific currency. If your new formatter supports any currency, you can set the 
`code` attribute to `*`. This will allow the formatter to be used for any currency.

## Parsing

Money parsers can help automate the process of extracting monetary value from text, making it more efficient and accurate.

The following parser classes are available as services:

 * Money\Parser\MoneyParser (alias for AggregateMoneyParser)
 * Money\Parser\IntMoneyParser (default if Intl extension is enabled)
 * Money\Parser\IntLocalizedDecimalParser (available if Intl is enabled)
 * Money\Parser\DecimalMoneyParser (default if Intl extension is disabled)
 * Money\Parser\BitcoinMoneyParser (available for `XBT` currency code)

You can use the `Money\MoneyParser` interface as a dependency for any service because it is an alias for the `Money\Parser\AggregateMoneyParser`
service, and it comes with default parsers.

To register a custom parser, you need to implement the `Money\MoneyParser` interface and tag the service with `money.parser`. 
This will enable you to use your custom parser to parse monetary values from a given text.

## Currency Conversion

To convert a `Money` instance from one currency to another, you need to use the `Money\Converter` service. This class relies 
on the `Currencies` and `Exchange` services. The `Exchange` service returns a `CurrencyPair`, which represents a combination 
of the base currency, counter currency, and the conversion ratio.

The following exchange classes are available as services:

 * Money\Exchange (alias for FixedExchange)
 * Money\Exchange\FixedExchange
 * Money\Exchange\IndirectExchange
 * Money\Exchange\ReversedCurrenciesExchange

In some cases, you may want the `Money\Converter` service to also resolve the reverse of a given `CurrencyPair` if the original 
cannot be found. To add this capability, you can inject the `Converter $reversedConverter` argument, which is an alias for 
`money.reversed_converter` service. If a reverse `CurrencyPair` can be found, it is used as a divisor of `1` to calculate 
the reverse conversion ratio.

To configure the `Money\Exchange\FixedExchange` service, you can use the following configuration:

```yaml
 money:
     exchanges:
         fixed:
             EUR:
                 USD: '1.10'
```

Note: Integration with third-party services like [Swap](https://github.com/florianv/swap) and [Exchanger](https://github.com/florianv/exchanger) 
is currently outside the scope of this bundle.

## Data Transfer Object

By design, the `Money\Money` value object is immutable, which means that it is not possible to change the original amount and currency
values after it is created. To address this, this bundle provides a DTO model called `MoneyDto` that can be used in various
situations, such as user inputs, API requests, form handling, validation, etc. This model allows you to modify the amount
and currency values, which can be useful in scenarios where you need to change these values before creating a new
`Money\Money` instance.

```php
 $dto = new MoneyDto(); // default null for amount and currency properties
 $dto = MoneyDto::fromMoney(Money::EUR(100)); // returns a new DTO instance
 $dto = MoneyDto::fromAmount(100); // default EUR currency
 $dto = MoneyDto::fromCurrency('USD'); // default 0 amount

 $money = $dto->toMoney(); // returns a new Money\Money instance
```

## Other Integrations

### Form

The Symfony `MoneyType` will be updated to derive the `scale` and `divisor` options from the `currency` value:

```php
$formBuilder->add('price', MoneyType::class, ['currency' => 'CUP'])
```

It is not supposed to work directly with the `Money\Money` object, as is typical, it expects a numeric property to be 
associated with this form field.

You can disable this integration by modifying the configuration:

```yaml
 money:
     form:
         enabled: false
```

### Twig

If you have installed `twig/twig` as your template engine, you can use the Twig filter provided to format your money objects
directly in any template page:

```twig
 {{ money|money_format }}
```

It will follow the same behavior as the `Money\Formatter\MoneyFormatter` service.

You can disable this integration by modifying the configuration:

```yaml
 money:
     twig:
         enabled: false
```

### Doctrine

Doctrine allows you to map an embedded object to a database column using the `Embedded` attribute and this bundle provides 
the `Money\Money` ORM mapping definitions for use with the Doctrine bundle, if it is enabled. This means that you can use 
Doctrine's entity manager to persist and retrieve your entities with the embedded money values, without having to manually 
configure the ORM mappings. This can simplify your development process and allow you to focus on other aspects of your application:

```php
 use Doctrine\ORM\Mapping\Embedded;
 use Money\Money;

 class Product
 {
     #[Embedded]
     private Money $price;
 }
 ```

Important: To ensure proper processing of the `Money\Money` mapping, it is important to register this bundle in `bundles.php`
before registering the `DoctrineBundle`.

```php
return [
    // ...
    Yceruto\MoneyBundle\MoneyBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    // ...
];
```

You can also use the fields of embedded classes that have been mapped using Doctrine in DQL (Doctrine Query Language) 
queries, just as if they were declared in the Product class itself:

```sql
SELECT p FROM Product p WHERE p.price.amount > 1000 AND p.price.currency.code = 'EUR' 
```

You can disable this integration by modifying the configuration:

```yaml
 money:
     doctrine:
         enabled: false
```

## License

This software is published under the [MIT License](LICENSE)
