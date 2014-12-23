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
		$controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, 'prevent_cache');
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
	public function preventDefault() {
		throw new Exception('preventDefault');
	}

	public function action_act_preprocess($event, $param)
	{
		global $auth, $INFO;
		global $template, $bezlang, $value, $errors;
		if ( ! $this->helper->user_viewer())
			return false;
		if ($this->action != '')
			$event->preventDefault();

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
		if ($this->action != '')
			$event->preventDefault();
		if (!isset($errors))
			$errors= array();
		foreach ($errors as $error) {
			echo '<div class="error">';
			echo $error;
			echo '</div>';
		}
		/*przerywamy wyświetlanie*/
		if (!$this->helper->user_viewer() || $this->norender)
			return false;

		$tpl = DOKU_PLUGIN."bez/tpl/".str_replace('/', '', $this->action).".php";
		if (file_exists($tpl)) {
			$bezlang = $this->lang;
			$helper = $this->helper;
			include_once $tpl;
		}
	}
	public function prevent_cache($event, $param) {
		$cache = $event->data;
		if ($cache->mode == 'xhtml') {
			$meta = p_get_metadata($cache->page, 'plugin_bez_nav');
			if (is_array($meta) && $meta['nocache']) {
				$event->preventDefault();
				$event->stopPropagation();
				$event->result = false;
			}
		}
	}
}
