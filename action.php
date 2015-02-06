<?php
 
if(!defined('DOKU_INC')) die();
 
class action_plugin_bez extends DokuWiki_Action_Plugin {

	private $helper;
	private $action = '';
	private $params = array();
	private $norender = false;

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	public function register(Doku_Event_Handler $controller)
	{
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'action_act_preprocess');
		$controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'tpl_act_render');
		$controller->register_hook('TEMPLATE_PAGETOOLS_DISPLAY', 'BEFORE', $this, 'tpl_pagetools_display');
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
		}
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
		global $auth, $INFO, $ID;
		global $template, $bezlang, $value, $errors;

		if ($this->action == '')
			return false;

		$event->preventDefault();

		if ( ! $this->helper->user_viewer())
			return false;

		$ctl= DOKU_PLUGIN."bez/ctl/".str_replace('/', '', $this->action).".php";
		if (file_exists($ctl)) {
			$bezlang = $this->lang;
			$helper = $this->helper;
			$params = $this->params;
			$uri = DOKU_URL . 'doku.php';
			$controller = $this;
			try {
				include_once $ctl;
			} catch(Exception $e) {
				/*preventDefault*/
				$this->norender = true;
			}
		}
	}

	public function tpl_act_render($event, $param)
	{
		global $template, $bezlang, $value, $errors;
		if ($this->action == '')
			return false;

		$event->preventDefault();

		/*przerywamy wyświetlanie*/
		if ($this->norender)
			return false;

		if (!$this->helper->user_viewer()) {
			html_denied();
			return false;
		}

		if (!isset($errors))
			$errors= array();
		foreach ($errors as $error) {
			echo '<div class="error">';
			echo $error;
			echo '</div>';
		}

		$tpl = DOKU_PLUGIN."bez/tpl/".str_replace('/', '', $this->action).".php";
		if (file_exists($tpl)) {
			$bezlang = $this->lang;
			$helper = $this->helper;
			include_once $tpl;
		}
	}
}
