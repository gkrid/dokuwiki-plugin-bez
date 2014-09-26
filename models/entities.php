<?php
include DOKU_PLUGIN."bez/models/connect.php";

class Entities extends Connect {
	public function get() {
		return array('Gruszka', 'Pietruszk');
	}
}

