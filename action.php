<?php
 
if(!defined('DOKU_INC')) die();

require_once DOKU_PLUGIN.'bez/mdl/model.php';


class action_plugin_bez extends DokuWiki_Action_Plugin {

	private $helper;
	private $action = '';
	private $params = array();
	private $norender = false;
	private $lang_code = '';
	
	private $model;

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	public function register(Doku_Event_Handler $controller)
	{
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'action_act_preprocess');
		$controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'tpl_act_render');
		$controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'BEFORE', $this, 'tpl_pagetools_display');
		$controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'include_dependencies', array());
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
		
		//Validetta
		
		// Adding a stylesheet 
		//~ $event->data["link"][] = array (
		  //~ "type" => "text/css",
		  //~ "rel" => "stylesheet", 
		  //~ "href" => DOKU_BASE.
			//~ "lib/plugins/bez/lib/validetta-v1.0.1-dist/validetta.min.css",
		//~ );
		
		//~ // Adding a JavaScript File
		//~ $event->data["script"][] = array (
		  //~ "type" => "text/javascript",
		  //~ "src" => DOKU_BASE.
			//~ "lib/plugins/bez/lib/validetta-v1.0.1-dist/validetta.min.js",
		  //~ "_data" => "",
		//~ );
		
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

	public function __construct()
	{
		global $ACT;
		$this->helper = $this->loadHelper('bez');
		$this->setupLocale();

		$id = $_GET['id'];
		$ex = explode(':', $id);
		if ($ex[0] == 'bez' && $ACT == 'show') {
			$this->action = $ex[1];
			$this->params = array_slice($ex, 2);
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
			$this->params = array_slice($ex, 3);
		}
		
		//set default filters
		if(!isset($_COOKIE[bez_tasks_filters]))
			setcookie("bez_tasks_filters[year]", date("Y"));
		if(!isset($_COOKIE[bez_issues_filters]))
			setcookie("bez_issues_filters[year]", date("Y"));
			}

	public function id() {
		$args = func_get_args();
		array_unshift($args, 'bez');
		if ($this->lang_code != '')
			array_unshift($args, $this->lang_code);
		return implode(':', $args);
	}

	public function issue_uri($id) {
		return '?id='.$this->id('issue', 'id', $id);
	}

	public function html_issue_link($id) {
		return '<a href="'.$this->issue_uri($id).'">#'.$id.'</a>';
	}
	public function html_task_link($issue, $task) {
		if ($issue == NULL)
			return '<a href="?id='.$this->id('show_task','tid', $task).'">#z'.$task.'</a>';
		else
			return '<a href="?id='.$this->id('issue_task', 'id', $issue, 'tid', $task).'">#'.$issue.' #z'.$task.'</a>';
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
		global $auth, $conf, $INFO, $ID;
		global $template, $bezlang, $value, $errors;

		try {
			$this->model =
				new BEZ_mdl_Model($auth, $INFO['client'], $conf['lang'], $this->lang);

			if ($this->action == '')
				return false;

			$event->preventDefault();


			if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer()))
				return false;

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
		} catch(Exception $e) {
			echo $e;
			/*preventDefault*/
			$this->norender = true;
		}
	}

	public function tpl_act_render($event, $param)
	{
		global $template, $bezlang, $value, $errors, $INFO;
		try {
			
			if ($this->action == '')
				return false;

			$event->preventDefault();

			/*przerywamy wyświetlanie*/
			if ($this->norender)
				return false;

			if	( ! ($this->helper->token_viewer() || $this->helper->user_viewer())) {
				html_denied();
				return false;
			}

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
		} catch(Exception $e) {
			echo $e;
		}
	}
}
