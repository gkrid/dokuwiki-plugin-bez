<?php
function usage() {
	echo "usage: cache.php -u username [-ric]\n";
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

$options = getopt("u:ric");
if (!array_key_exists('u', $options)) {
	usage();
}
$user = $options['u'];

$auth = new auth_plugin_authplain();

$helper = new helper_plugin_bez();
$model = new BEZ_mdl_Model($auth, $user, $helper, $conf);
$bezcache = new Bezcache();


if (array_key_exists('r', $options)) {
	foreach ($model->tasks->get_all() as $task) {
		echo "Updating cache of #z".$task->id."\n";
		$task->update_cache();
		$model->tasks->save($task);
		
		//old cache mechanizm
		$query = 'UPDATE tasks_cache
					SET task=:task, reason=:reason, toupdate=0 WHERE id=:id';
		$sth = $model->db->prepare($query);
		$sth->execute(array(
						':task' => $task->task_cache,
						':reason' => $task->reason_cache,
						':id' => $task->id
						));

	}
} elseif (array_key_exists('i', $options)) {
    foreach ($model->issues->get_all() as $issue) {
		echo "Updating cache of #".$issue->id."\n";
		$issue->update_cache();
		$model->issues->save($issue);
	}
} elseif (array_key_exists('c', $options)) {
    foreach ($model->commcauses->get_all() as $commcause) {
		echo "Updating cache of #c".$commcause->id."\n";
		$commcause->update_cache();
		$model->commcauses->save($commcause);
	}
} else {
	usage();
}
