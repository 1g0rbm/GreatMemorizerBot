<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Service;

use DateTime;
use DateTimeZone;
use Exception;

class TimeZoneConverter
{
    /**
     * @throws Exception
     */
    public function convert(string $time, string $fromTimeZone, string $toTimezone): DateTime
    {
        $dt = new DateTime($time, new DateTimeZone($fromTimeZone));
        $dt->setTimezone(new DateTimeZone($toTimezone));

        return $dt;
    }
}
