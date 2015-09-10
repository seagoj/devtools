<?php namespace Devtools\Currency;

class Currency
{
    protected $sigfig;
    protected $symbol;
    protected $separation;

    public function __construct($value = 0)
    {
        $this->value = $this->format($value);
    }

    public function value()
    {
        return (float)$this->value;
    }

    public function __toString()
    {
        return "{$this->symbol}{$this->value}";
    }

    public static function add(Currency $currency1, Currency $currency2)
    {
        $currencyType = self::type($currency1, $currency2);

        return new $currencyType(
            $currency1->value() + $currency2->value()
        );
    }

    public static function subtract(Currency $currency1, Currency $currency2)
    {
        $currencyType = self::type($currency1, $currency2);

        return new $currencyType(
            $currency1->value() - $currency2->value()
        );
    }

    public static function multiply(Currency $currency, $multiple)
    {
        $currencyType = get_class($currency);

        return new $currencyType(
            $currency->value * $multiple
        );
    }

    public static function divide(Currency $currency, $divisor)
    {
        $currentType = get_class($currency);

        return new $currentType(
            $currency->value / $divisor
        );
    }

    private static function type(Currency $currency1, Currency $currency2)
    {
        self::ensureTypeEquality($currency1, $currency2);

        return get_class($currency1);
    }

    private static function ensureTypeEquality(Currency $currency1, Currency $currency2)
    {
        if (get_class($currency1) !== get_class($currency2)) {
            throw new Exception('Cannot add two different denominations.');
        }
    }

    private function format($value)
    {
        return number_format(
            (float)$value,
            $this->sigfig,
            $this->separation,
            ''
        );
    }
}
