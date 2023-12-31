<?php
use MongoDB\BSON\UTCDateTime;

class MongoDate implements TypeInterface
{
    public $sec;
    public $usec;

    /**
     * Creates a new date. If no parameters are given, the current time is used.
     *
     * @param int $sec Number of seconds since January 1st, 1970
     * @param int $usec Microseconds
     */
    public function __construct($sec = 0, $usec = 0)
    {
        if (func_num_args() == 0) {
            $time = microtime(true);
            $sec = floor($time);
            $usec = ($time - $sec) * 1000000.0;
        } elseif ($sec instanceof UTCDateTime) {
            $msecString = (string) $sec;

            $sec = substr($msecString, 0, -3);
            $usec = ((int) substr($msecString, -3)) * 1000;
        }

        $this->sec = (int) $sec;
        $this->usec = (int) $this->truncateMicroSeconds($usec);
    }

    /**
     * Returns a string representation of this date
     * @return string
     */
    public function __toString()
    {
        return (string) sprintf('%.8f', $this->truncateMicroSeconds($this->usec) / 1000000) . ' ' . $this->sec;
    }

    /**
     * Converts this MongoDate to the new BSON UTCDateTime type
     *
     * @return UTCDateTime
     * @internal This method is not part of the ext-mongo API
     */
    public function toBSONType()
    {
        $milliSeconds = ($this->sec * 1000) + ($this->truncateMicroSeconds($this->usec) / 1000);

        return new UTCDateTime($milliSeconds);
    }

    /**
     * Returns a DateTime object representing this date
     * @link http://php.net/manual/en/mongodate.todatetime.php
     * @return DateTime
     */
    public function toDateTime()
    {
        $datetime = new \DateTime();
        $datetime->setTimezone(new \DateTimeZone("UTC"));
        $datetime->setTimestamp($this->sec);

        $microSeconds = $this->truncateMicroSeconds($this->usec);
        if ($microSeconds > 0) {
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s.u e', $datetime->format('Y-m-d H:i:s') . '.' . str_pad($microSeconds, 6, '0', STR_PAD_LEFT) . ' UTC');
        }

        return $datetime;
    }

    /**
     * @param int $usec
     * @return int
     */
    private function truncateMicroSeconds($usec)
    {
        return (int) floor($usec / 1000) * 1000;
    }
}
