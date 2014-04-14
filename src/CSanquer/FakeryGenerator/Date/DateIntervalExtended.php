<?php

namespace CSanquer\FakeryGenerator\Date;

use Doctrine\Common\Inflector\Inflector;

class DateIntervalExtended extends \DateInterval
{
    /**
     *
     * milliseconds
     *
     * @var int
     */
    public $ms;

    /**
     *
     * @param string $interval_spec
     * @param int    $ms            milliseconds
     */
    public function __construct($interval_spec, $ms = 0)
    {
        parent::__construct($interval_spec);
        $this->ms = (int) $ms;
    }

    /**
     *
     * @param  \DateInterval        $interval
     * @return DateIntervalExtended
     */
    public static function createFromDateInterval(\DateInterval $interval)
    {
        $extended = new self('PT0S', 0);
        $extended->y = $interval->y;
        $extended->m = $interval->m;
        $extended->d = $interval->d;
        $extended->h = $interval->h;
        $extended->i = $interval->i;
        $extended->s = $interval->s;
        $extended->invert = $interval->invert;
        $extended->days = $interval->days;
        if ($interval instanceof DateIntervalExtended) {
            $extended->ms = $interval->ms;
        }

        return $extended;
    }

    /**
     *
     * @param int $duration duration in ms
     *
     * @return DateIntervalExtended
     */
    public static function createFromMsDuration($duration)
    {
        $s = floor($duration / 1000);
        $ms = $duration % 1000;

        return new self('PT'.$s.'S', $ms);
    }

    /**
     * recalculate all interval components
     */
    public function recalculate()
    {
        $seconds = $this->toSeconds();

        $this->y = floor($seconds / 365 / 24 / 60 / 60);
        $seconds -= $this->y * 365 * 24 * 60 * 60;
        $this->m = floor($seconds / 30 / 24 / 60 / 60);
        $seconds -= $this->m * 30 * 24 * 60 * 60;
        $this->d = floor($seconds / 24 / 60 / 60);
        $seconds -= $this->d * 24 * 60 * 60;
        $this->h = floor($seconds/60/60);
        $seconds -= $this->h * 60 * 60;
        $this->i = floor($seconds/60);
        $seconds -= $this->i * 60;
        $this->s = floor($seconds);
        $this->ms = (int) (round(fmod($seconds, 1), 3)*1000);
    }

    /**
     * get the total number of seconds ( 1 year = 365 days and 1 month = 30 days)
     *
     * @return int|float total of seconds
     */
    public function toSeconds()
    {
        return $seconds = $this->y * 365 * 24 * 60 * 60 +
            $this->m * 30 * 24 * 60 * 60 +
            $this->d * 24 * 60 * 60 +
            $this->h * 60 * 60 +
            $this->i * 60 +
            $this->s +
            $this->ms / 1000;
    }

    /**
     *
     * @return string
     */
    public function prettyFormat()
    {
        $copy = static::createFromDateInterval($this);
        $copy->recalculate();

        $format = [];
        if ($copy->y !== 0) {
            $format[] = '%y '.$this->pluralize($copy->y, 'year');
        }

        if ($copy->m !== 0) {
            $format[] = '%m '.$this->pluralize($copy->m, 'month');
        }

        if ($copy->d !== 0) {
            $format[] = '%d '.$this->pluralize($copy->d, 'day');
        }

        if ($copy->h !== 0) {
            $format[] = '%h '.$this->pluralize($copy->h, 'hour');
        }

        if ($copy->i !== 0) {
            $format[] = '%i '.$this->pluralize($copy->i, 'minute');
        }

        if ($copy->s !== 0 || $copy->ms !==0) {
            $format[] = '%s'.($copy->ms !==0 ? '.'.str_pad($copy->ms, 3, 0, STR_PAD_LEFT) : '').' '.$this->pluralize($copy->s, 'second');
        }

        if (empty($format)) {
            $format[] = '0 second';
        }

        return $copy->format(implode(' ', $format));
    }

    /**
     * 
     * @param int $number
     * @param string $word
     * @return string
     */
    protected function pluralize($number, $word)
    {
        return $number > 1 ? Inflector::pluralize($word): $word;
    }
}
