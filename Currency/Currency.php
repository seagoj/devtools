<?php namespace Devtools\Currency;

abstract class Currency
{
    protected $sigfig;
    protected $symbol;
    protected $separation;

    public function __construct($value)
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
