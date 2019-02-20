<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if (!isset($_GET['t'])) {
    throw new bez\meta\PermissionDeniedException();
}

$this->model->factory('subscription')->mute($_GET['t']);

if (isset($_GET['oneclick'])) {
    die('List-Unsubscribe=One-Click');
}