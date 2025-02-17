<?php
namespace GCWorld\Utilities\Traits;

use DateInterval;
use DateTime;
use DateTimezone;
use Exception;

/**
 * Trait Time
 */
trait Time
{
    /**
     * @return array
     */
    public static function getTimeZoneList(): array
    {
        static $timezones = null;

        if (null === $timezones) {
            $timezones = [];
            $offsets   = [];
            $now       = new DateTime('now', new DateTimeZone('UTC'));

            foreach (\DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[]            = $offset = $now->getOffset();
                $timezones[$timezone] = '('.self::formatGMTOffset($offset).') '.self::formatTimezoneName($timezone);
            }

            array_multisort($offsets, $timezones);
        }

        return $timezones;
    }

    /**
     * @param mixed $offset
     *
     * @return string
     */
    protected static function formatGMTOffset(mixed $offset): string
    {
        $hours   = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));

        return 'UTC'.($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected static function formatTimezoneName(string $name): string
    {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);

        return $name;
    }



    /**
     * @param DateInterval $interval
     * @param array        $parts
     * @param string       $separator
     *
     * @return string
     */
    public static function formatInterval(
        DateInterval $interval,
        array $parts = ['y', 'm'],
        string $separator = ' '
    ): string
    {
        $bits = [];
        // years
        if (\in_array('y', $parts)) {
            if (1 == $interval->y) {
                $bits[] = '1 year';
            } elseif ($interval->y > 1) {
                $bits[] = '%y years';
            }
        }

        // months
        if (\in_array('m', $parts)) {
            if (0 == $interval->y && $interval->m <= 1) {
                $bits[] = '1 month';
            } elseif ($interval->m > 1) {
                $bits[] = '%m months';
            }
        }

        // days
        if (\in_array('d', $parts)) {
            if (1 == $interval->d) {
                $bits[] = '1 day';
            } elseif ($interval->d > 1) {
                $bits[] = '%d days';
            }
        }

        // hours
        if (\in_array('h', $parts)) {
            if (1 == $interval->h) {
                $bits[] = '1 hour';
            } elseif ($interval->h > 1) {
                $bits[] = '%h hours';
            }
        }

        if (\in_array('i', $parts)) {
            if (1 == $interval->i) {
                $bits[] = '1 minute';
            } elseif ($interval->i > 1) {
                $bits[] = '%i minutes';
            }
        }
        if (\in_array('s', $parts)) {
            if (1 == $interval->s) {
                $bits[] = '1 second';
            } elseif ($interval->s > 1) {
                $bits[] = '%s seconds';
            }
        }

        return $interval->format(\implode($separator, $bits));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function formatTime(string $value = '00:00'): string
    {
        $tmp = \explode(':', $value);
        if (3===\count($tmp)) {
            $tmp = [$tmp[0], $tmp[1]];
        }
        if (2 != \count($tmp)) {
            return '00:00';
        }

        if ($tmp[0] > 23 || $tmp[0] < 0) {
            $tmp[0] = 0;
        }
        if ($tmp[1] > 59 || $tmp[1] < 0) {
            $tmp[1] = 0;
        }

        $value  = \str_pad((string) \intval($tmp[0]), 2, '0', STR_PAD_LEFT).':';
        $value .= \str_pad((string) \intval($tmp[1]), 2, '0', STR_PAD_LEFT);

        return $value;
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    public static function isProperDate(string $date): bool
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);

        return false !== $dt && empty($dt->getLastErrors());
    }

    /**
     * @param int $minutes
     *
     * @return string
     */
    public static function minutesToHoursAndMinutes(int $minutes): string
    {
        $hrs  = \floor($minutes / 60);
        $mins = \str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT);

        return $hrs.':'.$mins;
    }



    /**
     * @param string      $min
     * @param string|null $max
     *
     * @return string
     */
    public static function timeAgo(string $min, ?string $max = null): string
    {
        if (null !== $max && $min > $max) {
            return '';
        }

        if (null === $max) {
            $max = 'now';
        }

        try {
            $minDate = new DateTime($min);
        } catch (Exception) {
            $minDate = new DateTime();
        }

        try {
            $maxDate = new DateTime($max);
        } catch (Exception) {
            $maxDate = new DateTime();
        }
        $interval = $minDate->diff($maxDate, true);

        $units = [
            'y' => 'year',
            'm' => 'month',
            'd' => 'day',
        ];

        $output = '';

        $callback = function ($word, $unit) use (&$output, $interval) {
            if ($interval->{$unit}) {
                $text    = $interval->format("%{$unit}");
                $output .= (\strlen($output) ? ' ' : '').$text." {$word}".(1 == $text ? '' : 's');

                return true;
            }

            return false;
        };

        \array_walk($units, $callback);

        // if less than one day
        if (!\strlen($output)) {
            $units = [
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            ];

            \array_walk($units, $callback);
        }

        return \strlen($output) ? $output : '';
    }
}