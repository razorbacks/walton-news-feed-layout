<?php

namespace razorbacks\walton\news;

use Exception;
use InvalidArgumentException;

class Backup
{
    protected $scheduler;
    protected $directory;

    public function setScheduler(Scheduler $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    public function setStorage($directory)
    {
        if (!is_string($directory)) {
            $type = gettype($directory);
            throw new InvalidArgumentException("Directory required, given type: $type");
        }

        $real = realpath($directory);

        if (false === $real) {
            throw new InvalidArgumentException("Directory required, given: $directory");
        }

        if (!is_dir($real)) {
            throw new InvalidArgumentException("Directory required, given: $real");
        }

        if (!is_writable($real)) {
            throw new InvalidArgumentException("$real is not writable.");
        }

        $this->directory = $real;
    }

    public function save()
    {
        if (is_null($this->scheduler)) {
            throw new Exception('Scheduler has not been set.');
        }

        if (is_null($this->directory)) {
            throw new Exception('Directory has not been set.');
        }

        $filename = 'SchedulerBackup-'.date("Y-m-d\TH:i:s.uP").'.crontab';
        $filename = "{$this->directory}/$filename";

        if (false === file_put_contents($filename, $this->scheduler->backup())) {
            throw new Exception('Could not write file to disk.');
        }

        return $filename;
    }
}
