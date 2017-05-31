<?php

use razorbacks\walton\news\Scheduler;
use razorbacks\walton\news\Backup;

class BackupTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        putenv('NEWS_PUBLICATION_STORAGE');

        $tmp = sys_get_temp_dir().'/SchedulerBackup-Latest.crontab';
        if (file_exists($tmp)) {
            unlink($tmp);
        }

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

    public function test_backs_up_publications_to_file()
    {
        $noise = '*/5 * * * * /path/to/job -with args';
        `(crontab -l 2>/dev/null; echo "$noise") | crontab -`;

        $publication = array(
            'categories' => array(6,7,8),
            'count' => 3,
            'view' => 'tile',
            'comments' => 'test',
        );

        $expected = 'categories\%5B0\%5D=6&categories\%5B1\%5D=7&categories\%5B2\%5D=8&count=3&view=tile';

        $scheduler = new Scheduler;
        $scheduler->createPublication($publication, $execute = false);

        $backup = new Backup;
        $backup->setScheduler($scheduler);
        $backup->setStorage(sys_get_temp_dir());
        $filename = $backup->save();

        $actual = file_get_contents($filename);
        $this->assertContains($expected, $actual);
        $this->assertNotContains($noise, $actual);
    }

    public function test_backs_up_last_ten_changes()
    {
        $tmp = sys_get_temp_dir();
        putenv("NEWS_PUBLICATION_STORAGE=$tmp");

        $scheduler = new Scheduler;

        $i = 12;
        while (--$i) {
            $scheduler->createPublication(
                array(
                    'categories' => array($i),
                    'count' => 2,
                    'view' => 'list',
                    'comments' => 'backup',
                ),
                $execute = false
            );
        }

        $files = glob("$tmp/SchedulerBackup-*.crontab");
        $this->assertCount(10, $files);

        $seven = 'categories\%5B0\%5D=7&count=2&view=list';
        $eleven = 'categories\%5B0\%5D=11&count=2&view=list';
        $actual = file_get_contents(end($files));
        $this->assertContains($seven, $actual);
        $this->assertNotContains($eleven, $actual);
    }
}
