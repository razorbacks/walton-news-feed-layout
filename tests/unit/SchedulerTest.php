<?php

use razorbacks\walton\news\Scheduler;
use razorbacks\walton\news\Backup;

class SchedulerTest extends PHPUnit_Framework_TestCase
{
    public function test_gets_available_minute()
    {
        // seed crontab with fixture which has every minute taken except 42
        $crontab = file_get_contents(__DIR__.'/../fixtures/crontabs/only42open.crontab');
        `echo "$crontab" | crontab -`;

        $expected = 42;

        $scheduler = new Scheduler;
        $actual = $scheduler->getAnOpenMinute();

        $this->assertSame($expected, $actual);
    }
}
