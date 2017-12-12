<?php

use \dokuwiki\plugin\bez;
 
if(!defined('DOKU_INC')) die();

//require_once DOKU_PLUGIN.'bez/mdl/model.php';
//
//require_once DOKU_PLUGIN.'bez/interfaces.php';
//require_once DOKU_PLUGIN.'bez/exceptions.php';
//
//spl_autoload_register(
//    function ($class) {
//        $file = DOKU_PLUGIN.'bez/inc/'.$class.'.class.php';
//        if (file_exists($file)) {
//            require_once $file;
//        }
//    }
//);

function bez_tpl_include(bez\meta\Tpl $tpl) {
    $file = DOKU_PLUGIN."bez/tpl/".str_replace('/', '', $tpl->action()).".php";
    if (file_exists($file)) {
        include $file;
    }
}

define('BEZ_NOTIFICATIONS_COOKIE_NAME', 'bez_notifications');

class action_plugin_bez_default extends DokuWiki_Action_Plugin {

	protected $action = '';
    protected $params = array();
    protected $lang_code = '';

    /** @var  bez\mdl\Model */
    protected $model;

    /** @var  bez\meta\Tpl */
    protected $tpl;

    protected $notifications = array();

    protected $errors = array();
    
    public function get_action() {
        return $this->action;
    }
    
    public function get_param($id) {
        return (isset($this->params[$id]) ? $this->params[$id] : '');
    }
    
    public function id() {
		$args = func_get_args();
        $elms = array();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $k => $v) {
                    $elms[] = $k;
                    $elms[] = $v;
                }
            } else {
                $elms[] = $arg;
            }
        }
		array_unshift($elms, 'bez');
        
        
		if ($this->lang_code !== '') {
			array_unshift($elms, $this->lang_code);
        }

		return implode(':', $elms);
    }
    
    public function url() {
        $args = func_get_args();
        if (count($args) > 0) {
            $id = call_user_func_array(array($this, 'id'), $args);
        } else {
            $id = $_GET['id'];
        }
        
        return DOKU_URL . 'doku.php?id=' . $id;
    }
    
//    public function model_factory($name) {
//        $factory = $name . 'Factory';
//        if (!property_exists($this->model, $factory)) {
//            throw new \Exception('unknown table: '.$name);
//        }
//        return $this->model->$factory;
//    }

    /**
     * @return mixed
     */
    public function get_model() {
        return $this->model;
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
    
//    private function param($id) {
//        return (isset($this->params[$id]) ? $this->params[$id] : '');
//    }

	/**
	 * Register its handlers with the DokuWiki's event controller
	 */
	public function register(Doku_Event_Handler $controller)
	{
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'setup_enviroment');
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
    
    public function setup_enviroment(Doku_Event $event, $param) {
        global $ACT, $auth, $conf, $INFO;
        
        if ($ACT !== 'show') {
            return;
        }

        $id = $_GET['id'];
		$ex = explode(':', $id);
 
        //check if we process BEZ
        if ($ex[0] !== 'bez' && $ex[1] !== 'bez') {
            return;
        }
        

        $lang_code = '';
        if ($ex[1] === 'bez') {
            $conf['lang'] = array_shift($ex);
            $this->lang_code = $conf['lang'];
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
        
        $this->model = new bez\mdl\Model($auth, $INFO['client'], $this, $conf);
        $this->tpl = new bez\meta\Tpl($this, $conf);
		
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

		} catch(Exception $e) {
            dbglog('plugin_bez', $e);
            if ($conf['allowdebug']) {
               dbg($e);
            }
            $this->tpl->prevent_rendering();
		}
	}

	public function tpl_act_render($event, $param)
	{
        global $conf;

        if ($this->action === '') {
            return false;
        }
        $event->preventDefault();
        
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

			bez_tpl_include($this->tpl);
            
        } catch(bez\meta\PermissionDeniedException $e) {
            dbglog('plugin_bez', $e);
            header('Location: ' . DOKU_URL . 'doku.php?id=' . $_GET['id'] . '&do=login');	
		} catch(Exception $e) {
			/*exception*/
            dbglog('plugin_bez', $e);
            if ($conf['allowdebug']) {
               dbg($e);
            }
		}
	}
}
