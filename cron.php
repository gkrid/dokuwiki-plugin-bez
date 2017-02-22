<?php
$inc = realpath(__DIR__.'/../../..');

define('DOKU_INC', $inc.'/');
define('DOKU_PLUGIN', $inc.'/lib/plugins/');
define('DOKU_CONF', $inc.'/conf/');

if (count($argv) < 2)
	die("podaj URI wiki dla którego odpalasz tego crona\n");
$URI = $argv[1];

if (isset($argv[2]) && $argv[2] == 'http')
	$http = 'http';
else
	$http = 'https';

$errors = array();

require_once DOKU_PLUGIN.'bez/lang/pl/lang.php';
$bezlang = $lang;

require_once DOKU_PLUGIN.'bez/models/issues.php';
require_once DOKU_PLUGIN.'bez/models/tasks.php';
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
require_once DOKU_PLUGIN.'auth.php';
require_once DOKU_PLUGIN.'authplain/auth.php';

require_once DOKU_PLUGIN.'bez/helper.php';
$helper = new helper_plugin_bez();

require_once 'cron-functions.php';

$auth = new auth_plugin_authplain();


send_message($URI, $http, $conf, $helper, $auth);
