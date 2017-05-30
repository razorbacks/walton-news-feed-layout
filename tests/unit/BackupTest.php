<?php

use razorbacks\walton\news\Scheduler;
use razorbacks\walton\news\Backup;

class BackupTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        `crontab -r`;
    }

    public function test_backs_up_publications()
    {
        $noise = '*/5 * * * * /path/to/job -with args';
        `(crontab -l 2>/dev/null; echo "$noise") | crontab -`;

        $publication = array(
            'categories' => array(1,2,3),
            'count' => 4,
            'view' => 'list',
            'comments' => 'test',
        );

        $expected = 'categories\%5B0\%5D=1&categories\%5B1\%5D=2&categories\%5B2\%5D=3&count=4&view=list';

        $scheduler = new Scheduler;
        $scheduler->createPublication($publication, $execute = false);

        $scheduler = new Scheduler;
        $actual = $scheduler->backup();
        $this->assertContains($expected, $actual);
        $this->assertNotContains($noise, $actual);

        $actual = `crontab -l`;
        $this->assertContains($expected, $actual);
        $this->assertContains($noise, $actual);
    }
}
