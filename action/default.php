<?php

use \dokuwiki\plugin\bez;

if(!defined('DOKU_INC')) die();

define('BEZ_NOTIFICATIONS_COOKIE_NAME', 'bez_notifications');

class action_plugin_bez_default extends action_plugin_bez_base {

	protected $action = '';
    protected $params = array();

    protected $notifications = array();

    protected $errors = array();

    public function get_action() {
        return $this->action;
    }

    public function get_param($id, $default='') {
        return (isset($this->params[$id]) ? $this->params[$id] : $default);
    }

    private function add_notification($value, $header=NULL) {
        if (isset($_COOKIE[BEZ_NOTIFICATIONS_COOKIE_NAME])) {
            $notifs = unserialize($_COOKIE[BEZ_NOTIFICATIONS_COOKIE_NAME]);
        } else {
            $notifs = array();
        }
        $notifs[] = array('value' => $value, 'header' => $header);
        setcookie(BEZ_NOTIFICATIONS_COOKIE_NAME, serialize($notifs));
    }

    private function flush_notifications() {
        if (!isset($_COOKIE[BEZ_NOTIFICATIONS_COOKIE_NAME])) {
            return array();
        }
        $this->notifications = unserialize($_COOKIE[BEZ_NOTIFICATIONS_COOKIE_NAME]);

        //remove cookie
        setcookie(BEZ_NOTIFICATIONS_COOKIE_NAME, serialize(array()));
    }

    private function add_error($value, $header=NULL) {
        $this->errors[] = array('value' => $value, 'header' => $header);
    }

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	public function register(Doku_Event_Handler $controller)
	{
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'setup_id');
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'setup_enviroment');
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'action_act_preprocess');
		$controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'tpl_act_render');
		$controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'BEFORE', $this, 'tpl_pagetools_display');
		$controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'include_dependencies', array());
		$controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');

        $controller->register_hook('PLUGIN_NOTIFICATION_REGISTER_SOURCE', 'AFTER', $this, 'add_notifications_source');
        $controller->register_hook('PLUGIN_NOTIFICATION_GATHER', 'AFTER', $this, 'add_notifications');
        $controller->register_hook('PLUGIN_NOTIFICATION_CACHE_DEPENDENCIES', 'AFTER', $this, 'add_notification_cache_dependencies');
	}

	public function include_dependencies(Doku_Event $event) {
		// Adding a stylesheet
		$event->data["link"][] = array (
		  "type" => "text/css",
		  "rel" => "stylesheet",
		  "href" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.timepicker-1.11.9-0/jquery.timepicker.css",
		);

		// Adding a JavaScript File
		$event->data["script"][] = array (
		  "type" => "text/javascript",
		  "src" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.timepicker-1.11.9-0/jquery.timepicker.min.js",
		  "defer" => "defer",
		  "_data" => "",
		);

		// Adding a JavaScript File
		$event->data["script"][] = array (
		  "type" => "text/javascript",
		  "src" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.datepair/datepair.js",
            "defer" => "defer",
		  "_data" => "",
		);

		// Adding a JavaScript File
		$event->data["script"][] = array (
		  "type" => "text/javascript",
		  "src" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.datepair/jquery.datepair.js",
            "defer" => "defer",
		  "_data" => "",
		);

		$event->data["link"][] = array (
		  "type" => "text/css",
		  "rel" => "stylesheet",
		  "href" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.form-validator/theme-default.min.css",
		);


		$event->data["script"][] = array (
		  "type" => "text/javascript",
		  "src" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.form-validator/jquery.form-validator.min.js",
            "defer" => "defer",
		  "_data" => "",
		);
	}

	public function setup_id(Doku_Event $event, $param) {
	    global $INFO, $ID;

        $id = $_GET['id'];
        $ex = explode(':', $id);

        //check if we process BEZ
        if ($ex[0] !== 'bez' && $ex[1] !== 'bez') {
            return;
        }

        $INFO['id'] = $id;
        $ID = $id;
    }

    public function setup_enviroment(Doku_Event $event, $param) {
        global $ACT, $conf, $ID;

        if ($ACT !== 'show') {
            return;
        }

		$ex = explode(':', $ID);

        //check if we process BEZ
        if ($ex[0] !== 'bez' && $ex[1] !== 'bez') {
            return;
        }

        if ($ex[1] === 'bez') {
            //pl is default language
            if ($ex[0] == 'pl') {
                //throw out "pl" and "bez"
                array_shift($ex);
                array_shift($ex);

                $url = call_user_func_array(array($this, 'url'), $ex);
                header("Location: $url");
            }
            $conf['lang'] = array_shift($ex);

            $this->localised = false;
        }
        //throw out "bez"
        array_shift($ex);

        $this->action = array_shift($ex);

        if (count($ex) % 2 !== 0) {
            throw new Exception('invalid params');
        }

        for ($i = 0; $i < count($ex); $i += 2) {
            $this->params[$ex[$i]] = $ex[$i+1];
        }

        $this->setupLocale();
        $this->createObjects();

        if (empty($conf['baseurl'])) {
            msg($this->getLang('info set baseurl'));
        }
        if (empty($conf['basedir'])) {
            msg($this->getLang('info set basedir'));
        }
    }

	/**
	 * handle ajax requests
	 */
	public function _ajax_call(Doku_Event $event, $param) {
		global $auth;
		if ($event->data !== 'plugin_bez') {
			return;
		}
		//no other ajax call handlers needed
		$event->stopPropagation();
		$event->preventDefault();

	}

	public function tpl_pagetools_display(Doku_Event $event, $param) {
		if ($this->action !== '') {
			$event->preventDefault();
        }
	}

	protected $prevent_rendering = false;

	public function action_act_preprocess(Doku_Event $event, $param)
	{
        global $conf;

        if ($this->action === '') {
            return;
        }

        $event->preventDefault();
		try {
            $this->flush_notifications();

			$ctl = DOKU_PLUGIN."bez/ctl/".str_replace('/', '', $this->action).".php";

			if (file_exists($ctl)) {
				include $ctl;
			}
        } catch(bez\meta\ValidationException $e) {
            foreach ($e->get_errors() as $field => $error_code) {
                $lang = $this->getLang($field);
                if ($lang != '') {
                    $field = $lang;
                }
                $this->add_error(
                    $this->getLang('validate_' . $error_code),
                    $field);
            }

            $this->tpl->set_values($_POST);

        } catch(bez\meta\PermissionDeniedException $e) {
            dbglog('plugin_bez', $e);
            header('Location: ' . DOKU_URL . 'doku.php?id=' . $_GET['id'] . '&do=login');
//        } catch (\PHPMailer\PHPMailer\Exception $e) {
//            msg($e->getMessage(), -1);
		} catch(Exception $e) {
            dbglog('plugin_bez', $e);
            if ($conf['allowdebug']) {
               dbg($e);
            } else {
                msg($e->getMessage(), -1);
            }
            $this->prevent_rendering = true;
		}
	}

	public function tpl_act_render($event, $param)
	{
        global $conf;

        if ($this->action === '') {
            return false;
        }
        $event->preventDefault();

        if ($this->prevent_rendering) return;

		try {

			foreach ($this->errors as $error) {
				echo '<div class="error">';
                if ($error['header'] === NULL) {
					echo $error['value'];
				} else {
					echo '<strong>'.$error['header'].'</strong>: '.$error['value'];
				}
				echo '</div>';
			}

            foreach ($this->notifications as $note) {
                echo '<div class="info">';
				if ($note['header'] === NULL) {
					echo $note['value'];
				} else {
					echo $note['header'].': <strong>'.$note['value'].'</strong>';
				}
				echo '</div>';
            }

			$this->bez_tpl_include(str_replace('/', '', $this->get_action()));

        } catch(bez\meta\PermissionDeniedException $e) {
            dbglog('plugin_bez', $e);
		} catch(Exception $e) {
			/*exception*/
            dbglog('plugin_bez', $e);
            if ($conf['allowdebug']) {
               dbg($e);
            }
		}
	}

	public function add_notifications_source(Doku_Event $event)
    {
        $event->data[] = 'bez:problems_without_tasks';
        $event->data[] = 'bez:problems_coming';
        $event->data[] = 'bez:problems_outdated';
        $event->data[] = 'bez:tasks_coming';
        $event->data[] = 'bez:tasks_outdated';
    }

    public function add_notification_cache_dependencies(Doku_Event $event)
    {
        if (!preg_grep('/^bez:.*/', $event->data['plugins'])) return;

        /** @var \helper_plugin_bez_db $db_helper */
        $db_helper = plugin_load('helper', 'bez_db');
        $event->data['dependencies'][] = $db_helper->getDB()->getAdapter()->getDbFile();
    }

    public function add_notifications(Doku_Event $event)
    {
        if (!preg_grep('/^bez:.*/', $event->data['plugins'])) return;

        $user = $event->data['user'];
        $this->createObjects(true);

        if (in_array('bez:problems_without_tasks', $event->data['plugins'])) {
            $threads = $this->get_model()->factory('thread')->get_all(array(
                                                                          'type' => 'issue',
                                                                          'task_count' => '0',
                                                                          'state' => 'opened',
                                                                          'coordinator' => $user
                                                                      ));
            /** @var bez\mdl\Thread $thread */
            foreach ($threads as $thread) {
                $link = '<a href="' . $this->url('thread', 'id', $thread->id) . '">';
                $link .= '#' . $thread->id;
                $link .= '</a>';

                $full = sprintf($this->getLang('notification problems_without_tasks'), $link);
                $event->data['notifications'][] = [
                    'plugin' => 'bez:problems_without_tasks',
                    'id' => 'thread:' . $thread->id,
                    'full' => $full,
                    'brief' => $link,
                    'timestamp' => strtotime($thread->last_activity_date)
                ];
            }
        }

        if (in_array('bez:problems_coming', $event->data['plugins'])) {
            $threads = $this->get_model()->factory('thread')->get_all(array(
                'type' => 'issue',
                'priority' => '1',
                'coordinator' => $user
            ));
            /** @var bez\mdl\Thread $thread */
            foreach ($threads as $thread) {
                $link = '<a href="' . $this->url('thread', 'id', $thread->id) . '">';
                $link .= '#' . $thread->id;
                $link .= '</a>';

                $full = sprintf($this->getLang('notification problems_coming'), $link);
                $event->data['notifications'][] = [
                    'plugin' => 'bez:problems_coming',
                    'id' => 'thread:' . $thread->id,
                    'full' => $full,
                    'brief' => $link,
                    'timestamp' => strtotime($thread->last_activity_date)
                ];
            }
        }

        if (in_array('bez:problems_outdated', $event->data['plugins'])) {
            $threads = $this->get_model()->threadFactory->get_all(array(
                'type' => 'issue',
                'priority' => '2',
                'coordinator' => $user
            ));
            /** @var bez\mdl\Thread $thread */
            foreach ($threads as $thread) {
                $link = '<a href="' . $this->url('thread', 'id', $thread->id) . '">';
                $link .= '#' . $thread->id;
                $link .= '</a>';

                $full = sprintf($this->getLang('notification problems_outdated'), $link);
                $event->data['notifications'][] = [
                    'plugin' => 'bez:problems_outdated',
                    'id' => 'thread:' . $thread->id,
                    'full' => $full,
                    'brief' => $link,
                    'timestamp' => strtotime($thread->last_activity_date)
                ];
            }
        }

        if (in_array('bez:tasks_coming', $event->data['plugins'])) {
            $tasks = $this->get_model()->factory('task')->get_all(array(
                'priority' => '1',
                'assignee' => $user
            ));
            /** @var bez\mdl\Thread $thread */
            foreach ($tasks as $task) {
                $link = '<a href="' . $this->url('task', 'tid', $task->id) . '">';
                $link .= '#z' . $task->id;
                $link .= '</a>';

                $full = sprintf($this->getLang('notification tasks_coming'), $link);
                $event->data['notifications'][] = [
                    'plugin' => 'bez:tasks_coming',
                    'id' => 'task:' . $task->id,
                    'full' => $full,
                    'brief' => $link,
                    'timestamp' => strtotime($task->plan_date)
                ];
            }
        }

        if (in_array('bez:tasks_outdated', $event->data['plugins'])) {
            $tasks = $this->get_model()->factory('task')->get_all(array(
                'priority' => '2',
                'assignee' => $user
            ));
            /** @var bez\mdl\Thread $thread */
            foreach ($tasks as $task) {
                $link = '<a href="' . $this->url('task', 'tid', $task->id) . '">';
                $link .= '#z' . $task->id;
                $link .= '</a>';

                $full = sprintf($this->getLang('notification tasks_outdated'), $link);
                $event->data['notifications'][] = [
                    'plugin' => 'bez:tasks_outdated',
                    'id' => 'task:' . $task->id,
                    'full' => $full,
                    'brief' => $link,
                    'timestamp' => strtotime($task->plan_date)
                ];
            }
        }
    }
}
