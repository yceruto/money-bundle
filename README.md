# MoneyBundle (WIP)

Symfony integration of the https://github.com/moneyphp/money library.

## Install

    $ composer require yceruto/money-bundle

It is important to ensure that the bundle is added to the `config/bundles.php` file of the project.

## Currencies

Applications often require a specific subset of currencies from different data sources. To facilitate this, you can 
implement the `Money\Currencies` interface, which provides a list of available currencies and the subunit for each currency.

The `Currencies` interface is also an alias for the `Money\Currencies\AggregateCurrencies` service, which 
comes with the following providers out of the box:
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
