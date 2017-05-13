<?php
 
if(!defined('DOKU_INC')) die();

require_once DOKU_PLUGIN.'bez/mdl/model.php';
require_once DOKU_PLUGIN.'bez/html.php';
require_once DOKU_PLUGIN.'bez/exceptions.php';

class action_plugin_bez extends DokuWiki_Action_Plugin {

	private $helper;
	private $action = '';
	private $params = array();
	private $norender = false;
	private $lang_code = '';
	
	private $model_object = null;

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	public function register(Doku_Event_Handler $controller)
	{
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'action_act_preprocess');
		$controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'tpl_act_render');
		$controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'BEFORE', $this, 'tpl_pagetools_display');
		$controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'include_dependencies', array());
		$controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');
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
		  "_data" => "",
		);
		
		// Adding a JavaScript File
		$event->data["script"][] = array (
		  "type" => "text/javascript",
		  "src" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.datepair/datepair.js",
		  "_data" => "",
		);
		
		// Adding a JavaScript File
		$event->data["script"][] = array (
		  "type" => "text/javascript",
		  "src" => DOKU_BASE.
			"lib/plugins/bez/lib/jquery.datepair/jquery.datepair.js",
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
		  "_data" => "",
		);
		
		
	}
	
    //to powinno powędrować do __construct(!), ale nie może z powdu potoku dokuwiki,
    //dlatego decyduję się na leniwą konkretyzację
	public function __get($name) {
		global $auth, $conf, $INFO;
		if ($name === 'model') {
			if ($this->model_object === null) {
				if ($INFO === null) {
					$INFO = pageinfo();
				}
				$this->model_object =
				new BEZ_mdl_Model($auth, $INFO['client'], $this, $conf);
			}
			return $this->model_object;
		}
	}

	public function __construct() {
		global $ACT;
		$this->helper = $this->loadHelper('bez');
		$this->setupLocale();

		$id = $_GET['id'];
		$ex = explode(':', $id);
		if ($ex[0] == 'bez' && $ACT == 'show') {
			$this->action = $ex[1];
			$this->params = $ex;
		/*BEZ w innym języku*/
		} else if ($ex[1] == 'bez' && $ACT == 'show') {
			$l = $ex[0];
			$p = DOKU_PLUGIN.'bez/lang/';
			$f = $p.$ex[0].'/lang.php';
			if ( ! file_exists($f))
				$f = $p.'en/lang.php';

			$this->lang_code = $l;
			include $f;
			$this->lang = $lang;

			$this->action = $ex[2];
			$this->params = array_slice($ex, 1);
		}
		
		//set default filters
		if(!isset($_COOKIE[bez_tasks_filters])) {
			setcookie("bez_tasks_filters[year]", date("Y"));
		}
		if(!isset($_COOKIE[bez_issues_filters])) {
			setcookie("bez_issues_filters[year]", date("Y"));
		}
	}

	public function id() {
		$args = func_get_args();
		array_unshift($args, 'bez');
		if ($this->lang_code != '')
			array_unshift($args, $this->lang_code);
		return implode(':', $args);
    }

//	public function issue_uri($id) {
//		return '?id='.$this->id('issue', 'id', $id);
//	}
//
//	public function html_issue_link($id) {
//		return '<a href="'.$this->issue_uri($id).'">#'.$id.'</a>';
//	}
//	public function html_task_link($issue, $task) {
//		if ($issue == NULL)
//			return '<a href="?id='.$this->id('show_task','tid', $task).'">#z'.$task.'</a>';
//		else
//			return '<a href="?id='.$this->id('issue_task', 'id', $issue, 'tid', $task).'">#'.$issue.' #z'.$task.'</a>';
//	}
//	
	/**
	 * handle ajax requests
	 */
	function _ajax_call(Doku_Event $event, $param) {
		global $auth;
		if ($event->data !== 'plugin_bez') {
			return;
		}
		//no other ajax call handlers needed
		$event->stopPropagation();
		$event->preventDefault();
	 
		//data
		$data = array();
	 
		//json library of DokuWiki
		$json = new JSON();
		
		$action = $_POST['action'];
		try {
			if ($action === 'commcause_delete') {
				$kid = $_POST['kid'];
				
				$commcause = $this->model->commcauses->get_one($kid);
				$this->model->commcauses->delete($commcause);
				
				$data['state'] = 'ok';
			}
		} catch(Exception $e) {
			$data['state'] = 'error';
			$data['msg'] = strval($e);
		}
	 
		//set content type
		header('Content-Type: application/json');
		echo $json->encode($data);
	}
	


	public function tpl_pagetools_display($event, $param) {
		if ($this->action != '') 
			$event->preventDefault();
	}
	public function preventDefault() {
		throw new Exception('preventDefault');
	}

	public function action_act_preprocess($event, $param)
	{
		global $INFO;
		global $template, $bezlang, $value, $errors;

		try {
			if ($this->action == '')
				return false;

			$event->preventDefault();


//			if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer()))
//				return false;

			$ctl= DOKU_PLUGIN."bez/ctl/".str_replace('/', '', $this->action).".php";
			if (file_exists($ctl)) {
				$bezlang = $this->lang;
				$helper = $this->helper;
				$helper->lang_code = $this->lang_code;

				$params = $this->params;
				$nparams = array();
				for ($i = 0; $i < count($params); $i += 2)
					$nparams[$params[$i]] = $params[$i+1];

				$uri = DOKU_URL . 'doku.php';
				$controller = $this;
				include_once $ctl;
			}
        } catch(PermissionDeniedException $e) {
			header('Location: ' . DOKU_URL . 'doku.php?id=' . $_GET['id'] . '&do=login');
		} catch(Exception $e) {
            echo nl2br($e);
			/*preventDefault*/
			$this->norender = true;
		}
	}

	public function tpl_act_render($event, $param)
	{
		global $template, $bezlang, $value, $errors;
		try {
			
			if ($this->action == '')
				return false;

			$event->preventDefault();

			/*przerywamy wyświetlanie*/
			if ($this->norender)
				return false;

//			if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer())) {
//				html_denied();
//				return false;
//			}

			if (!isset($errors)) {
				$errors= array();
			}

			foreach ($errors as $field => $error) {
				echo '<div class="error">';
				if ($field != '' && isset($bezlang[$field])) {
					echo '<b>'.$bezlang[$field].':</b> ';
					if (isset($bezlang['validate_'.$error])) {
						echo $bezlang['validate_'.$error];
					} else {
						echo $error;
					}
				} else {
					echo $error;
				}
				echo '</div>';
			}

			$tpl = DOKU_PLUGIN."bez/tpl/".str_replace('/', '', $this->action).".php";
			if (file_exists($tpl)) {
				$bezlang = $this->lang;
				$helper = $this->helper;
				$helper->lang_code = $this->lang_code;

				$params = $this->params;
				$nparams = array();
				for ($i = 0; $i < count($params); $i += 2)
					$nparams[$params[$i]] = $params[$i+1];

				include_once $tpl;
			}
        } catch(PermissionDeniedException $e) {
			header('Location: ' . DOKU_URL . 'doku.php?id=' . $_GET['id'] . '&do=login');
		} catch(Exception $e) {
			/*exception*/
		}
	}
}
