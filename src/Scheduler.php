<?php
namespace razorbacks\walton\news\feed;

use Crontab\Crontab;
use Crontab\Job;

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
		foreach($this->getJobs() as $job){
			$minute = filter_var($job->getMinute(), FILTER_VALIDATE_INT);
			if(is_int($minute)){
				$taken []= $minute;
			}
		}

		$available = array_diff($open, $taken);
		return $available[array_rand($available)];
	}
}
