<?php
namespace razorbacks\walton\news;

use Crontab\Crontab;
use Crontab\Job;
use Exception;

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
		return unserialize(
			preg_replace(
				'/^O:\d+:"[^"]++"/', 
				'O:'.strlen($class).':"'.$class.'"',
				serialize($job)
			)
		);
	}

	public function getPublications(){
		$publications = array();
		foreach($this->getJobs() as $job){
			$publications []= $this->castJobToPublication($job);
		}
		return $publications;
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

	public function createPublication($array){
		$minute = $this->getAnOpenMinute();
		$publication = new Publication($array, $minute);

		// un-escape in-memory command %
		// https://github.com/yzalis/Crontab/issues/34
		$command = str_replace('\\%', '%', $publication->getCommand());
		exec($command, $output, $return);
		if($return != 0){
			throw new Exception('Error: '.implode(PHP_EOL, $output));
		}

		$this->addJob($publication)->write();
	}
}
