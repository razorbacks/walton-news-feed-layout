<?php
namespace razorbacks\walton\news;

use Crontab\Crontab;
use Crontab\Job;
use Exception;
use InvalidArgumentException;

class Scheduler extends Crontab {
	protected function castJobToPublication(Job $job) {
		$class = __NAMESPACE__ . '\Publication';
		/**
		* This is a beautifully ugly hack.
		* https://gist.github.com/duaiwe/960035
		*
		* First, we serialize our object, which turns it into a string, allowing
		* us to muck about with it using standard string manipulation methods.
		*
		* Then, we use preg_replace to change it's defined type to the class
		* we're casting it to, and then serialize the string back into an
		* object.
		*/
		$publication = unserialize(
			preg_replace(
				'/^O:\d+:"[^"]++"/', 
				'O:'.strlen($class).':"'.$class.'"',
				serialize($job)
			)
		);

		$publication->parseCommand();

		return $publication;
	}

	public function getPublications(){
		$publications = array();
		foreach($this->getJobs() as $job){
			if($job instanceof Publication){
				$publication = $job;
			} else {
				$publication = $this->castJobToPublication($job);
			}
			if($publication->valid){
				$publications[$publication->getHash()] = $publication;
			}
		}
		return $publications;
	}

	public function runPublication($hash){
		$publications = $this->getPublications();
		if(!isset($publications[$hash])){
			throw new InvalidArgumentException("Publication does not exist.");
		}

		exec($publications[$hash]->getCommand(), $output, $return);
		if($return != 0){
			throw new Exception('Run Publication Error: '.implode(PHP_EOL, $output));
		}
	}

	public function deletePublication($hash){
		$publications = $this->getPublications();
		if(!isset($publications[$hash])){
			throw new InvalidArgumentException("Publication does not exist.");
		}
		$this->removeJob($publications[$hash])->write();

		$backup = new Backup($this);
		$backup->latest();
	}

	public function getAnOpenMinute(){
		// minutes between 5-55  to avoid on the hour jobs
		for($i = 5; $i < 56; $i++){
			$open []= $i;
		}

		// minutes that are taken by other jobs
		$taken = array();
		foreach($this->getJobs() as $job){
			$minute = filter_var($job->getMinute(), FILTER_VALIDATE_INT);
			if(is_int($minute)){
				$taken []= $minute;
			}
		}

		$available = array_diff($open, $taken);
		if(empty($available)){
			return $open[array_rand($open)];
		}
		return $available[array_rand($available)];
	}

	public function createPublication($array, $execute = true){
		$minute = $this->getAnOpenMinute();
		$publication = new Publication($array, $minute);

		// un-escape in-memory command %
		// https://github.com/yzalis/Crontab/issues/34
		$command = str_replace('\\%', '%', $publication->getCommand());
		if ($execute) {
			exec($command, $output, $return);
			if($return != 0){
				throw new Exception('Error: '.implode(PHP_EOL, $output));
			}
		}

		$this->addJob($publication)->write();

		$backup = new Backup($this);
		$backup->latest();
	}

	public function backup()
	{
		$temp = new Crontab($parseExistingCrontab = false);

		foreach ($this->getPublications() as $publication) {
			$temp->addJob($publication);
		}

		return $temp->render();
	}
}
