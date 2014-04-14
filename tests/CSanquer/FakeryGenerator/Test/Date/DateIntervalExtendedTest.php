<?php

namespace CSanquer\FakeryGenerator\Test\Date;

use CSanquer\FakeryGenerator\Date\DateIntervalExtended;

class DateIntervalExtendedTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider providerConstruct
     */
    public function testConstruct($values, $expected)
    {
        if (isset($values['ms'])) {
            $dateInterval = new DateIntervalExtended($values['interval'], $values['ms']);
        } else {
            $dateInterval = new DateIntervalExtended($values['interval']);
        }

        $this->assertInstanceOf('\\DateInterval', $dateInterval);

        $interval = [
            'y' => $dateInterval->y,
            'm' => $dateInterval->m,
            'd' => $dateInterval->d,
            'h' => $dateInterval->h,
            'i' => $dateInterval->i,
            's' => $dateInterval->s,
            'ms' => $dateInterval->ms,
        ];

        $this->assertEquals($expected, $interval);
    }

    public function providerConstruct()
    {
        return [
            // data set #0
            [
                [
                    'interval' => 'PT170S',
                ],
                [
                    'y' => 0,
                    'm' => 0,
                    'd' => 0,
                    'h' => 0,
                    'i' => 0,
                    's' => 170,
                    'ms' => 0,
                ],
            ],
            // data set #1
            [
                [
                    'interval' => 'PT170S',
                    'ms' => null,
                ],
                [
                    'y' => 0,
                    'm' => 0,
                    'd' => 0,
                    'h' => 0,
                    'i' => 0,
                    's' => 170,
                    'ms' => 0,
                ],
            ],
            // data set #2
            [
                [
                    'interval' => 'PT5890S',
                    'ms' => 152,
                ],
                [
                    'y' => 0,
                    'm' => 0,
                    'd' => 0,
                    'h' => 0,
                    'i' => 0,
                    's' => 5890,
                    'ms' => 152,
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerCreateFromDateInterval
     */
    public function testCreateFromDateInterval($dateInterval, $expected)
    {
        $dateIntervalExtended = DateIntervalExtended::createFromDateInterval($dateInterval);
        $this->assertInstanceOf('\\DateInterval', $dateIntervalExtended);
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Date\\DateIntervalExtended', $dateIntervalExtended);

        $interval = [
            'y' => $dateIntervalExtended->y,
            'm' => $dateIntervalExtended->m,
            'd' => $dateIntervalExtended->d,
            'h' => $dateIntervalExtended->h,
            'i' => $dateIntervalExtended->i,
            's' => $dateIntervalExtended->s,
            'invert' => $dateIntervalExtended->invert,
            'days' => $dateIntervalExtended->days,
            'ms' => $dateIntervalExtended->ms,
        ];

        $this->assertEquals($expected, $interval);
    }

    public function providerCreateFromDateInterval()
    {
        return [
            // data set #0
            [
                new \DateInterval('P1Y3M20DT10H36M48S'),
                [
                    'y' => 1,
                    'm' => 3,
                    'd' => 20,
                    'h' => 10,
                    'i' => 36,
                    's' => 48,
                    'invert' => 0,
                    'days' => false,
                    'ms' => 0,
                ],
            ],
            // data set #1
            [
                new DateIntervalExtended('P1Y3M20DT10H36M48S', 356),
                [
                    'y' => 1,
                    'm' => 3,
                    'd' => 20,
                    'h' => 10,
                    'i' => 36,
                    's' => 48,
                    'invert' => 0,
                    'days' => false,
                    'ms' => 356,
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerCreateFromMsDuration
     */
    public function testCreateFromMsDuration($value, $expected)
    {
        $dateInterval = DateIntervalExtended::createFromMsDuration($value);
        $this->assertInstanceOf('\\DateInterval', $dateInterval);
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Date\\DateIntervalExtended', $dateInterval);

        $interval = [
            'y' => $dateInterval->y,
            'm' => $dateInterval->m,
            'd' => $dateInterval->d,
            'h' => $dateInterval->h,
            'i' => $dateInterval->i,
            's' => $dateInterval->s,
            'ms' => $dateInterval->ms,
        ];

        $this->assertEquals($expected, $interval);
    }

    public function providerCreateFromMsDuration()
    {
        return [
            // data set #0
            [
                15646149040,
                [
                    'y' => 0,
                    'm' => 0,
                    'd' => 0,
                    'h' => 0,
                    'i' => 0,
                    's' => 15646149,
                    'ms' => 40,
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerToSeconds
     */
    public function testToSeconds($values, $expected, $expectedType)
    {
        if (isset($values['ms'])) {
            $dateInterval = new DateIntervalExtended($values['interval'], $values['ms']);
        } else {
            $dateInterval = new DateIntervalExtended($values['interval']);
        }
        $seconds = $dateInterval->toSeconds();
        $this->assertInternalType($expectedType, $seconds);
        $this->assertEquals($expected, $seconds);
    }

    public function providerToSeconds()
    {
        return [
            // data set #0
            [
                [
                    'interval' => 'P1Y3M12DT5H27M56S',
                    'ms' => 0,
                ],
                40368476,
                'int'
            ],
            // data set #0
            [
                [
                    'interval' => 'P1Y3M12DT5H27M56S',
                    'ms' => 259,
                ],
                40368476.259,
                'float'
            ],
        ];
    }

    /**
     * @dataProvider providerRecalculate
     */
    public function testRecalculate($values, $expected)
    {
        if (isset($values['ms'])) {
            $dateInterval = new DateIntervalExtended($values['interval'], $values['ms']);
        } else {
            $dateInterval = new DateIntervalExtended($values['interval']);
        }

        $dateInterval->recalculate();

        $interval = [
            'y' => $dateInterval->y,
            'm' => $dateInterval->m,
            'd' => $dateInterval->d,
            'h' => $dateInterval->h,
            'i' => $dateInterval->i,
            's' => $dateInterval->s,
            'ms' => $dateInterval->ms,
        ];

        $this->assertEquals($expected, $interval);
    }

    public function providerRecalculate()
    {
        return [
            // data set #0
            [
                [
                    'interval' => 'PT5890S',
                    'ms' => 152,
                ],
                [
                    'y' => 0,
                    'm' => 0,
                    'd' => 0,
                    'h' => 1,
                    'i' => 38,
                    's' => 10,
                    'ms' => 152,
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerPrettyFormat
     */
    public function testPrettyFormat($values, $expected)
    {
        $dateInterval = new DateIntervalExtended($values['interval'], $values['ms']);
        $this->assertEquals($expected, $dateInterval->prettyFormat());
    }

    public function providerPrettyFormat()
    {
        return [
            // data set #0
            [
                [
                    'interval' => 'P459DT5890S',
                    'ms' => 152,
                ],
                '1 year 3 months 4 days 1 hour 38 minutes 10.152 seconds',
            ],
            // data set #1
            [
                [
                    'interval' => 'PT1M',
                    'ms' => 38,
                ],
                '1 minute 0.038 second',
            ],
            // data set #2
            [
                [
                    'interval' => 'PT0S',
                    'ms' => 0,
                ],
                '0 second',
            ],
        ];
    }
}
