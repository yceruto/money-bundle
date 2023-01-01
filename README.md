# MoneyBundle

Symfony integration of the https://github.com/moneyphp/money library.

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
by implementing the `Currencies` interface and tagging it with `money.currencies` as a service.

The `CurrencyList` provider retrieves the currencies from the money configuration:

    money:
        currencies:
            FOO: 2

The list consists of pairs of currency codes (strings) and subunits (integers). You can also use this configuration to 
override the subunit for `ISOCurrencies`.

## Formatters

Formatters can be helpful when you need to display a monetary value in a specific format. They allow you to convert a 
money object into a human-readable string, making it easier to present financial data to users. By using formatters, you 
can ensure that the money values you display are clear and easy to understand.

The `MoneyFormatter` interface is an alias for the `Money\Formatter\AggregateMoneyFormatter` service, which comes with
the following providers by default:

 * Money\Formatter\IntMoneyFormatter (default if Intl extension is enabled)
 * Money\Formatter\IntLocalizedMoneyFormatter (available if Intl is enabled)
 * Money\Formatter\DecimalMoneyFormatter (default if Intl extension is disabled)
 * Money\Formatter\BitcoinMoneyFormatter (available for `XBT` currency code)

To register a custom formatter, you will need to implement the `MoneyFormatter` interface and tag the service with 
`money.formatter` and the currency `code` attribute that the formatter supports. This will allow you to use your custom 
formatter to format monetary values in a specific currency. If your new formatter supports any currency, you can set the 
`code` attribute to `*`. This will allow the formatter to be used for any currency in conjunction with 
`Money\Formatter\AggregateMoneyFormatter` service.
