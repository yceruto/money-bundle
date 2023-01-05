# MoneyBundle

Symfony integration of the https://github.com/moneyphp/money library.

![ci](https://github.com/yceruto/money-bundle/actions/workflows/ci.yml/badge.svg)

## Install

    $ composer require yceruto/money-bundle

It is important to ensure that the bundle is added to the `config/bundles.php` file of the project.

## Currencies

Applications often require a specific subset of currencies from different data sources. To facilitate this, you can 
implement the `Money\Currencies` interface, which provides a list of available currencies and the subunit for each currency.

The `Currencies` interface is also an alias for the `Money\Currencies\AggregateCurrencies` service, which 
comes with the following providers by default:

 * Money\Currencies\CurrencyList;
 * Money\Currencies\ISOCurrencies;
 * Money\Currencies\BitcoinCurrencies;
 * Money\Currencies\CryptoCurrencies;

The providers are injected into the `AggregateCurrencies` service in the specified order, and you can add more providers 
by implementing the `Money\Currencies` interface and tagging it with `money.currencies` as a service.

The `Money\Currencies\CurrencyList` provider retrieves the currencies from the money configuration:

    money:
        currencies:
            FOO: 2

The list consists of pairs of currency codes (strings) and subunits (integers). You can also use this configuration to 
override the subunit for `Money\Currencies\ISOCurrencies`.

## Formatters

Money formatters can be helpful when you need to display a monetary value in a specific format. They allow you to convert a 
money object into a human-readable string, making it easier to present financial data to users. By using formatters, you 
can ensure that the money values you display are clear and easy to understand.

You can use the `Money\MoneyFormatter` interface as a dependency for any service because it is an alias for the `Money\Formatter\AggregateMoneyFormatter` 
service, and it comes with the following formatters by default:

 * Money\Formatter\IntMoneyFormatter (default if Intl extension is enabled)
 * Money\Formatter\IntLocalizedMoneyFormatter (available if Intl is enabled)
 * Money\Formatter\DecimalMoneyFormatter (default if Intl extension is disabled)
 * Money\Formatter\BitcoinMoneyFormatter (available for `XBT` currency code)

To register a custom formatter, you will need to implement the `Money\MoneyFormatter` interface and tag the service with 
`money.formatter` and the currency `code` attribute that the formatter supports. This will allow you to use your custom 
formatter to format monetary values in a specific currency. If your new formatter supports any currency, you can set the 
`code` attribute to `*`. This will allow the formatter to be used for any currency.

## Parsers

Money parsers can help automate the process of extracting monetary value from text, making it more efficient and accurate.

You can use the `Money\MoneyParser` interface as a dependency for any service because it is an alias for the `Money\Parser\AggregateMoneyParser`
service, and it comes with the following parsers by default:

 * Money\Parser\IntMoneyParser (default if Intl extension is enabled)
 * Money\Parser\IntLocalizedDecimalParser (available if Intl is enabled)
 * Money\Parser\DecimalMoneyParser (default if Intl extension is disabled)
 * Money\Parser\BitcoinMoneyParser (available for `XBT` currency code)

To register a custom parser, you need to implement the `Money\MoneyParser` interface and tag the service with `money.parser`. 
This will enable you to use your custom parser to parse monetary values from a given text.

## Twig filter

    {{ money|money_format }}

## Form

The Symfony `MoneyType` will be updated to derive the `scale` and `divisor` options from the `currency` value.
