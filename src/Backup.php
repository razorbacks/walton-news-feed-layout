<?php

namespace razorbacks\walton\news;

use Exception;
use InvalidArgumentException;
use Dotenv\Dotenv;

class Backup
{
    protected $scheduler;
    protected $directory;

    public function __construct(Scheduler $scheduler = null)
    {
        if (isset($scheduler)) {
            $this->setScheduler($scheduler);
        }

        $dotenv = new Dotenv(dirname(__DIR__));
        $dotenv->load();

        $storage = getenv('NEWS_PUBLICATION_STORAGE');
        if ( empty($storage) ) {
            $storage = __DIR__.'/../publications';
        }

        $this->setStorage($storage);
    }

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
        return $this->write(date("Y-m-d\TH:i:s.uP"));
    }

    public function latest()
    {
        return $this->write('Latest');
    }

    protected function write($filename)
    {
        if (is_null($this->scheduler)) {
            throw new Exception('Scheduler has not been set.');
        }

        $filename = "{$this->directory}/SchedulerBackup-$filename.crontab";

        if (false === file_put_contents($filename, $this->scheduler->backup())) {
            throw new Exception('Could not write file to disk.');
        }

        return $filename;
    }
}
