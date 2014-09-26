<?php
 
if(!defined('DOKU_INC')) die();
 
class action_plugin_bez extends DokuWiki_Action_Plugin {

	private $helper;
	private $action = '';
	private $params = array();

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	public function register(Doku_Event_Handler $controller)
	{
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'action_act_preprocess');
		$controller->register_hook('TPL_ACT_RENDER', 'BEFORE', $this, 'tpl_act_render');
	}

	public function __construct()
	{
		global $ACT;
		$this->helper = $this->loadHelper('bez');

		$id = $_GET['id'];
		$ex = explode(':', $id);
		if ($ex[0] == 'bez' && $ACT == 'show') {
			$this->action = $ex[1];
			$this->params = array_slice($ex, 2);
		}
	}

	public function action_act_preprocess($event, $param)
	{
		global $template, $value;
		if ( ! $this->helper->user_can_view())
			return false;
		if ($this->action != '')
			$event->preventDefault();

		$ctl= DOKU_PLUGIN."bez/ctl/".str_replace('/', '', $this->action).".php";
		if (file_exists($ctl)) {
			$helper = $this->helper;
			$params = $this->params;
			include $ctl;
		}
		if ($redirect != '')
			header('Location: '.$redirect);
	}

	public function tpl_act_render($event, $param)
	{
		global $errors, $template, $value, $lang;
		if ( ! $this->helper->user_can_view())
			return false;
		if ($this->action != '')
			$event->preventDefault();
		if (!isset($errors))
			$errors= array();
		foreach ($errors as $error) {
			echo '<div class="error">';
			echo $error;
			echo '</div>';
		}
		$tpl = DOKU_PLUGIN."bez/tpl/".str_replace('/', '', $this->action).".php";
		if (file_exists($tpl)) {
			$this->setupLocale();
			$lang = $this->lang;
			$helper = $this->helper;
			include $tpl;
		}
	}
}
