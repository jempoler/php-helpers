<?php
/*
  +------------------------------------------------------------------------+
  | ManaCode PHP Helpers                                                   |
  +------------------------------------------------------------------------+
  | Copyright (c) 2012-2016 manacode (https://github.com/manacode)         |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.                                 |
  |                                                                        |
  +------------------------------------------------------------------------+
  | Authors: Leonardus Agung <jempoler.com@gmail.com>                      |
  |                                                                        |
  +------------------------------------------------------------------------+
*/

namespace Manacode\Helpers;
class DateTime extends \DateTime
{
	protected $dateFormat = "Y-m-d";
	protected $timeFormat = "H:i:s";
	protected $timeReference = "gmt";
	protected $timeZone = "UTC";
	
	function __construct($time = "now", $timezone = NULL, $config = array()) {
		parent::__construct($time, $timezone);
		if (isset($config['dateFormat'])) {
			$this->dateFormat = $config['dateFormat'];
		}
		if (isset($config['timeFormat'])) {
			$this->timeFormat = $config['timeFormat'];
		}
		if (isset($config['timeReference'])) {
			$this->timeReference= $config['timeReference'];
		}
		// $this->set_timeZone();
		date_default_timezone_set($timezone::getName());
	}

	function set_timeZone($tz="") {
		if ($tz=="") {
			$tz = $this->timeZone;
		} else {
			$this->timeZone = $tz;
		}
		date_default_timezone_set($tz);
	}

	function set_dateFormat($df) {
		$this->dateFormat = $df;
	}

	function set_timeFormat($tf) {
		$this->timeFormat = $tf;
	}

	function set_timeReference($tr) {
		$this->timeReference = $tr;
	}

	function get_dateFormat() {
		return $this->dateFormat;
	}

	function get_timeFormat() {
		return $this->timeFormat;
	}

	function get_timeReference() {
		return $this->timeReference;
	}

// ------------------------------------------------------------------------

	/**
	 * Get "now" time
	 *
	 * Returns time() or its GMT equivalent based on the time reference
	 *
	 * @access	public
	 * @return	integer
	 **/
	function now() {
		if (strtolower($this->timeReference) == 'gmt') {
			$now = time();
			$system_time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));
			if (strlen($system_time) < 10) {
				$system_time = time();
			}
			return $system_time;
		} else {
			return time();
		}
	}

	function mdate($date_format = '', $time = '') {
		if ($date_format == '') {
			$date_format = $this->dateFormat;
		}
		if ($time == '') {
			$time = $this->now();
		}
		return date($date_format, $time);
	}
	
	function days_in_month($month = 0, $year = '') {
		if ($month < 1 OR $month > 12) {
			return 0;
		}
		if ( ! is_numeric($year) OR strlen($year) != 4) {
			$year = date('Y');
		}
		if ($month == 2) {
			if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0)) {
				return 29;
			}
		}

		$days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		return $days_in_month[$month - 1];
	}

	function get_timezones() {
		$cc_timezone = array();
		foreach(\DateTimeZone::listIdentifiers() as $tz) {
	    $current_tz = new \DateTimeZone($tz);
	    $offset =  $current_tz->getOffset($this);
	    $transition =  $current_tz->getTransitions($this->getTimestamp(), $this->getTimestamp());
	    $abbr = $transition[0]['abbr'];
			$cc_timezone[$tz]= $abbr. ' '. $this->formatOffset($offset);
		}
		return $cc_timezone;
	}
	
	function formatOffset($offset) {
		$hours = $offset / 3600;
		$remainder = $offset % 3600;
		$sign = $hours > 0 ? '+' : '-';
		$hour = (int) abs($hours);
		$minutes = (int) abs($remainder / 60);
		if ($hour == 0 AND $minutes == 0) {
			$sign = ' ';
		}
		return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');
	}

  function today() {
    return $this->mdate($this->dateFormat);
  }

  function todaytime() {
    return $this->mdate($this->dateFormat . " " . $this->timeFormat);
  }

  function get_first_date_this_week() {
    return $this->mdate($this->dateFormat, strtotime('this week', time()));
  }

  function get_last_date_this_week() {
    return $this->mdate($this->dateFormat, strtotime('this week +6 days', time()));
  }

  function get_first_date_this_month($showtime=false) {
    if ($showtime===true) {
      return $this->mdate("%Y-%m-01 00:00:01");
    } else {
      return $this->mdate("%Y-%m-01");
    }
  }

  function get_last_date_this_month() {
    return $this->mdate("%Y-%m-%t");
  }

  function get_first_date_last_month($m=1) {
    return $this->mdate($this->dateFormat, mktime(0, 0, 0, $this->mdate("%m")-$m, 1, $this->mdate("%Y")));
  }

  function get_last_date_last_month($m=1) {
    return $this->mdate($this->dateFormat, mktime(24, 0, 0, $this->mdate("%m")-($m-1), -1, $this->mdate("%Y")));
  }

  function get_first_date_this_year($showtime=false) {
    if ($showtime===true) {
      return $this->mdate("%Y-01-01 00:00:01");
    } else {
      return $this->mdate("%Y-01-01");
    }
  }

  function get_last_date_this_year() {
    return $this->mdate($this->dateFormat, mktime(24, 0, 0, 1, -1, $this->mdate("%Y")+1));
  }

  function get_first_date_last_year($y=1) {
    return $this->mdate($this->dateFormat, mktime(0, 0, 0, 1, 1, $this->mdate("%Y")-$y));
  }

  function get_last_date_last_year($y=1) {
    return $this->mdate($this->dateFormat, mktime(24, 0, 0, 1, -1, $this->mdate("%Y")-($y-1)));
  }
  
  function get_month_name($m) {
		$dt = DateTime::createFromFormat('!m', $m);
		return $dt->format('F');
 	}
}