<?php
namespace razorbacks\walton\news\feed;

use Crontab\Job;
use DateTime;

class Publication extends Job {
	public function getNextRuntime(){
		$date = new DateTime(date('h:i:s'));
		$dminute = (int)$date->format('i');
		$jminute = (int)$this->getMinute();

		// if minute passed, increment hour
		if($jminute <= $dminute){
			$date->modify("+1 hour");
		}

		$date->setTime((int)$date->format('h'), $jminute);
		return $date->format('h:i A');
	}
}
