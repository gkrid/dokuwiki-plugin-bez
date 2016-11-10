<?php
function usage() {
	echo "usage: cache.php -u username [-r]\n";
	exit(1);
}

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
require_once DOKU_INC.'inc/init.php';

require_once DOKU_INC.'inc/plugin.php';
require_once DOKU_INC.'inc/plugincontroller.class.php';
require_once DOKU_INC.'inc/pluginutils.php';

require_once DOKU_INC.'inc/events.php';
require_once DOKU_INC.'inc/parserutils.php';
require_once DOKU_INC.'inc/confutils.php';


require_once DOKU_PLUGIN.'auth.php';
require_once DOKU_PLUGIN.'authplain/auth.php';

require_once DOKU_PLUGIN.'bez/mdl/model.php';

require_once DOKU_PLUGIN.'bez/helper.php';

$options = getopt("u:r");
if (!array_key_exists('u', $options)) {
	usage();
}
$user = $options['u'];

$auth = new auth_plugin_authplain();


$model = new BEZ_mdl_Model($auth, $user);
$helper = new helper_plugin_bez();


if (array_key_exists('r', $options)) {
	foreach ($model->tasks->get_all() as $task) {
		$task->update_cache();
		$model->tasks->save($task);
	}
} else {
	usage();
}
