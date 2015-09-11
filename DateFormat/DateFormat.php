<?php namespace Devtools\DateFormat;

class DateFormat
{
    protected $format;
    protected $separator;
    protected $day;
    protected $month;
    protected $year;

    public function __construct($date = null)
    {
        if ($date == null) {
            return;
        }
        $dateTime = explode(' ', $date);
        $date = $dateTime[0];
        $this->time = isset($dateTime[1])
            ? $dateTime[1]
            : '';
        $dateArray = explode($this->separator, $date);
        foreach (str_split($this->format) as $element) {
            switch($element) {
            case 'M':
            case 'm':
                $this->month = array_shift($dateArray);
                break;
            case 'D':
            case 'd':
                $this->day = array_shift($dateArray);
                break;
            case 'Y':
            case 'y':
                $this->year = array_shift($dateArray);
                break;
            }
        }
    }

    public function __toString()
    {
        if (!isset($this->month)) {
            return '';
        }

        $output = array();
        foreach (str_split($this->format) as $element) {
            switch($element) {
            case 'M';
            case 'm':
                $output[] = $this->month;
                break;
            case 'D';
            case 'd':
                $output[] = $this->day;
                break;
            case 'Y';
            case 'y':
                $output[] = $this->year;
                break;
            }
        }

        $date = implode($this->separator, $output);
        $time = !empty($this->time) ? " {$this->time}" : '';

        return "{$date}{$time}";
    }

    public function __get($property)
    {
        if (!in_array($property, array('format', 'separator'))) {
            return $this->$property;
        }
    }

    public function from(DateFormat $originalFormat)
    {
        $this->month = $originalFormat->month;
        $this->day   = $originalFormat->day;
        $this->year  = $originalFormat->year;
        return $this;
    }
}
