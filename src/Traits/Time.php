<?php
namespace GCWorld\Utilities\Traits;

/**
 * Trait Time
 */
trait Time
{
    /**
     * @return array
     */
    public static function getTimeZoneList()
    {
        static $timezones = null;

        if (null === $timezones) {
            $timezones = [];
            $offsets   = [];
            $now       = new \DateTime('now', new \DateTimeZone('UTC'));

            foreach (\DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new \DateTimeZone($timezone));
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
    protected static function formatGMTOffset($offset)
    {
        $hours   = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));

        return 'UTC'.($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    protected static function formatTimezoneName(string $name)
    {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);

        return $name;
    }
}