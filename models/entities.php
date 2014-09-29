<?php
include_once DOKU_PLUGIN."bez/models/connect.php";

class Entities extends Connect {
	public function get() {
		return array('Gruszka', 'Pietruszk');
	}
	public function ids() {
		return array_keys($this->get());
	}
}

