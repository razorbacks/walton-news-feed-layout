<?php
namespace razorbacks\walton\news\feed;

use Crontab\Crontab;
use Crontab\Job;

class CustomCrontab extends Crontab {
	protected function castJobToCustomJob(Job $job) {
		$class = __NAMESPACE__ . '\CustomJob';
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
	public function getCustomJobs(){
		foreach($this->getJobs() as $job){
			$jobs []= $this->castJobToCustomJob($job);
		}
		return $jobs;
	}
}
