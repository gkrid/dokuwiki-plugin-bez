<?php

function usage() {
	echo "usage: migrate.php -u username -r [nr to run]\n";
	exit(1);
}

$options = getopt("u:r:");
if (!array_key_exists('u', $options)) {
	usage();
}

$INFO = array();
$INFO['client'] = $options['u'];



$inc = realpath(__DIR__.'/../../..');

define('DOKU_INC', $inc.'/');
define('DOKU_PLUGIN', $inc.'/lib/plugins/');
define('DOKU_CONF', $inc.'/conf/');

$config_cascade = array();
require_once DOKU_INC.'inc/config_cascade.php';
$conf = array();
// load the global config file(s)
foreach (array('default','local','protected') as $config_group) {
	if (empty($config_cascade['main'][$config_group])) continue;
	foreach ($config_cascade['main'][$config_group] as $config_file) {
		if (file_exists($config_file)) {
		include($config_file);
		}
	}
}

require_once DOKU_INC.'inc/plugin.php';
require_once DOKU_INC.'lib/plugins/admin.php';

require_once DOKU_PLUGIN.'auth.php';
require_once DOKU_PLUGIN.'authplain/auth.php';

$auth = new auth_plugin_authplain();

require_once DOKU_PLUGIN.'bez/admin/dbschema.php';


$bez_dbschema = new admin_plugin_bez_dbschema();

$i = 0;
foreach ($bez_dbschema->get_actions() as $act) {
    echo $i.'. '.$act[0].' - ';
    if (call_user_func(array($bez_dbschema, $act[1]))) {
        echo 'OK';
    } else {
        echo 'Not applied';
    }
    echo "\n";
    $i += 1;
}

if (array_key_exists('r', $options)) {
    $op = (int)$options['r'];
    $actions = $bez_dbschema->get_actions();
    if (!isset($actions[$op])) {
        usage();
    }
    if (call_user_func(array($bez_dbschema, $actions[$op][1]))) {
        echo "Already applied\n";
        exit(1);
    }
    call_user_func(array($bez_dbschema, $actions[$op][2]));
}
