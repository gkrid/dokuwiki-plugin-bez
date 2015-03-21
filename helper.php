<?php

if(!defined('DOKU_INC')) die();

include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/tokens.php";

class helper_plugin_bez extends dokuwiki_plugin
{
	/*najniższe stadium rozwoju użytkownika. Może oglądać tylko to na co pozwala mu jego token*/
	public function token_viewer() {
		global $ID;

			
		if (!isset($_GET['t']))
			return false;

		$toko = new Tokens();
		if ($toko->check(trim($_GET['t']), $ID))
			return true;

		return false;
	}
	public function user_viewer() {
		global $ID;

		if (auth_quickaclcheck($ID) >= AUTH_READ)
			return true;

		return false;
	}

	public function user_editor() {
		global $ID;

		if (auth_quickaclcheck($ID) >= AUTH_EDIT)
			return true;

		return false;
	}

	public function user_coordinator($issue_id) {
		global $INFO;
		global $bezlang, $errors;

		if (self::user_admin())
			return true;

		if (self::user_editor()) {
			$issuo = new Issues();
			$issue = $issuo->get_clean($issue_id);
			if ($issue['coordinator'] == $INFO['client'])
				return true;
		}
		return false;
	}

	public function user_admin() {
		global $INFO, $auth;

		$userd = $auth->getUserData($INFO['client']); 
		if ($userd && in_array('admin', $userd['grps']))
				return true;

		return false;
	}

	public function wiki_parse($content) {
		$info = array();
		return p_render('xhtml',p_get_instructions($content), $info);
	}

	public function mailto($to, $subject, $body) {
		return 'mailto:'.$to.'?subject='.rawurlencode($subject).'&body='.rawurlencode($body);
	}

	public function days($diff) {
		$time_str = '';
		$minutes = floor($diff/60);
		if ($minutes > 0) {
			$hours = floor($minutes/60);
			if ($hours > 0) {
				$days = floor($hours/24);
				if ($days > 0) {
					$time_str = $days.' '.$this->getLang('days');
				} else {
					$time_str = $hours.' '.$this->getLang('hours');
				}
			} else {
				$time_str = $minutes.' '.$this->getLang('minutes');
			}
		} else {
			$time_str = $diff.' '.$this->getLang('seconds');
		}
		return $time_str;
	}

	public function string_time_to_now($value) {
		$diff = time() - $value;
		if ($diff < 5) {
			return $this->getLang('just_now');
		}
		return self::days($diff).' '.$this->getLang('ago');
	}
	public function time2date($time) {
		return date('Y-m-d', $time);
	}
	public function array_data_sort($a, $b) {
		$a = $a['date'];
		$b = $b['date'];
		if ($a == $b)
			return 0;
		return ($a > $b) ? -1 : 1;
	}
	public function darray_merge($a, $b) {
		$adict = array();
		$bdict = array();
		foreach ($a as $k => $v)
			$adict[] = $k;
		foreach ($b as $k => $v)
			$bdict[] = $k;

		sort($adict);
		sort($bdict);

		$i = $j = 0;
		$array = array();
		for (;;) {
			/*czy to już koniec*/
			if ($i == count($a)) {
				for ($n = $j; $n < count($b); $n++) {
					$k = $bdict[$n];
					$array[$k] = $b[$k];
				}
				break;
			} else if ($j == count($b)) {
				for ($n = $i; $n < count($a); $n++) {
					$k = $adict[$n];
					$array[$k] = $a[$k];
				}
				break;
			}

			if ($adict[$i] == $bdict[$j]) {
				$k = $adict[$i];
				$array[$k] = array_merge($a[$k], $b[$k]);
				usort($array[$k], array($this, 'array_data_sort'));
				$i++;
				$j++;
			} else if ($adict[$i] > $bdict[$j]) {
				$k = $bdict[$j];
				$array[$k] = $b[$k];
				$j++;
			} else {
				$k = $adict[$i];
				$array[$k] = $a[$k];
				$i++;
			}
		}

		return $array;
	}
	public function days_array_merge() {
		$array = array();
		foreach (func_get_args() as $arg) 
			$array = self::darray_merge($array, $arg);

		return $array;
	}
	public function mail($to, $subject, $body, $URI='', $debug = false) {
		if ($debug) {
			echo $to."\n";
			echo $subject."\n";
			echo $body;
			echo "\n\n";
			return;
		}
		if ($URI == '')
			$URI = $_SERVER['SERVER_NAME'];

		$headers = 	"From: noreply@$URI\n";
		$headers .= "Content-Type: text/plain; charset=UTF-8\n"; 
		$headers .= "Content-Transfer-Encoding: 8bit\n";

		mail($to, $subject, $body, $headers);
	}
}
