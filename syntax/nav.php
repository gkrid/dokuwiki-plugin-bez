<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 */

// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';
include_once DOKU_PLUGIN."bez/models/issues.php";
include_once DOKU_PLUGIN."bez/models/causes.php";
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/taskactions.php";

include_once DOKU_PLUGIN."bez/mdl/model.php";

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bez_nav extends DokuWiki_Syntax_Plugin {
	private $value = array();
	private $lang_code = '';
	private $default_lang = 'pl';

	private $auth, $model, $validator;
	
    function getPType() { return 'block'; }
    function getType() { return 'substition'; }
    function getSort() { return 99; }


    function connectTo($mode) {
		$this->Lexer->addSpecialPattern('~~BEZNAV~~',$mode,'plugin_bez_nav');
    }

	function __construct() {
		global $conf, $INFO, $auth;

		$id = $_GET['id'];

		/*usuń : z początku id - link bezwzględny*/
		if ($id[0] == ':')
			$id = substr($id, 1);

		$ex = explode(':', $_GET['id']);

		//wielojęzyczność
		if ($ex[1] == 'bez') {
			$this->lang_code = $ex[0];
			$ex = array_slice($ex, 1);

			$old_lang = $conf['lang'];
			$conf['lang'] = $this->lang_code;
			$this->setupLocale();
			$conf['lang'] = $old_lang;

		} else {
			$this->lang_code = $conf['lang'];
		}

		for ($i = 0; $i < count($ex); $i += 2)
			$this->value[urldecode($ex[$i])] = urldecode($ex[$i+1]);
			
		$this->model = new BEZ_mdl_Model($auth, $INFO['client'], $this->lang_code);
	}

    function handle($match, $state, $pos, Doku_Handler $handler)
    {
		return true;
    }

    function render($mode, Doku_Renderer $R, $pass) {
		global $INFO, $auth;

		$helper = $this->loadHelper('bez');
		if ($mode != 'xhtml' || !$helper->user_viewer()) return false;

        $R->info['cache'] = false;

		$data = array(
			'bez:start' => array('id' => 'bez:start', 'type' => 'd', 'level' => 1, 'title' => $this->getLang('bez')),
		);


		$data['bez:issues'] = array('id' => 'bez:issues', 'type' => 'd', 'level' => 2, 'title' => $this->getLang('bds_issues'));

		$task_pages = array('issue_tasks', 'task_form', 'issue_task');
		$cause_pages = array('issue_causes', 'issue_cause', 'cause_form', 'issue_cause_task');
		$issue_pages = array_merge(array('issue', 'rr', '8d'), $task_pages, $cause_pages);

		if ($this->value['bez'] == 'issues' || $this->value['bez'] == 'issue_report') {
			if ($helper->user_editor()) {
				$data['bez:issue_report'] = array('id' => 'bez:issue_report', 'type' => 'f', 'level' => 3, 'title' => $this->getLang('bds_issue_report'));
			}
			$data['bez:issues']['open'] = true;
		}
		if (in_array($this->value['bez'], $issue_pages) || ($this->value['bez'] == 'issue_report' && isset($this->value['id']))) {
			if ($helper->user_editor()) {
				$data['bez:issue_report'] = array('id' => 'bez:issue_report', 'type' => 'f', 'level' => 3, 'title' => $this->getLang('bds_issue_report'));
			}
			$data['bez:issues']['open'] = true;
			$id = (int)$this->value[id];

			$isso = new Issues();
			$issue_opened = $isso->opened($id);
			$issue_proposal = $isso->is_proposal($id);
			
			$type = 'f';

			$pid = "bez:issue:id:$id";
			$data[$pid] = array('id' => $pid, 'type' => $type, 'level' => 3,
												'title' => "#$id", 'open' => true);
		}
		
		
		$data['bez:tasks'] = array('id' => 'bez:tasks', 'type' => 'd', 'level' => 2, 'title' => $this->getLang('bez_tasks'));
		
		if ($this->value['bez'] == 'tasks' || $this->value['bez'] == 'show_task'
			|| $this->value['bez'] == 'task_form_plan'
			|| $this->value['bez'] == 'issue_task'
			|| $this->value['bez'] == 'task_form'
			|| $this->value['bez'] == 'task_form'
			|| $this->value['bez'] == 'issue_cause_task') {
			$data['bez:tasks']['open'] = true;

			if (isset($this->value['year']))
				$year = $this->value['year'];
			else
				$year = date('Y');


			if (isset($this->value['tid'])) {
				$tasko = new Tasks();
				$this->value['tasktype']  = $tasko->get_type($this->value['tid']);
			}
			
			
			$page_id = "bez:tasks:tasktype:-none:year:$year";
			if (isset($this->value['tid']) && $this->value['tasktype'] == '') {
				$data[$page_id] = array('id' => $page_id, 'type' => 'd', 'level' => 3, 'title' => $this->getLang('tasks_no_type'), 'open' => true);
				
				$page_id = 'bez:show_task:tid:'.$this->value['tid'];
				//$page_id = $_GET['id'];
				$data[$page_id.':perspective:task'] = array('id' => $page_id, 'type' => 'f', 'level' => 4, 'title' => '#z'.$this->value['tid']);

			} else {
				$data[$page_id] = array('id' => $page_id, 'type' => 'f', 'level' => 3, 'title' => $this->getLang('tasks_no_type'));
			}
			
			$tasktypes = $this->model->tasktypes->get_all();
			foreach ($tasktypes as $tasktype) {
				$page_id = "bez:tasks:tasktype:".$tasktype->id.":year:$year";

				if ($this->value['tasktype'] == $tasktype->id) {	
					$data[$page_id] = array('id' => $page_id, 'type' => 'd', 'level' => 3, 'title' => $tasktype->type, 'open' => true);
	
					//~ if ($tasktype->get_level() >= 15) {
					$report_id = "bez:task_form:tasktype:".$tasktype->id;
					$data[$report_id] = array('id' => $report_id, 'type' => 'f', 'level' => 4, 'title' => $this->getLang('bds_task_report'));
					//~ }
					if (isset($this->value['tid'])) {
						$page_id = 'bez:show_task:tid:'.$this->value['tid'];
						//$page_id = $_GET['id'];
						$data[$page_id.':perspective:task'] = array('id' => $page_id, 'type' => 'f', 'level' => 4, 'title' => '#z'.$this->value['tid']);
					}
				} else {
					$data[$page_id] = array('id' => $page_id, 'type' => 'f', 'level' => 3, 'title' => $tasktype->type);
				}
			}
		}

		$isso = new Issues();
		$year_now = (int)date('Y');
		$mon_now = (int)date('n');

		$data['bez:report'] = array('id' => 'bez:report', 'type' => 'd', 'level' => 2, 'title' => $this->getLang('report'));
		if ($this->value['bez'] == 'report') {
			$data['bez:report']['open'] = true;

			$oldest = $isso->get_oldest_close_date();
			$year_old = (int)date('Y', $oldest);
			$mon_old = (int)date('n', $oldest);

			$mon = $mon_old;
			for ($year = $year_old; $year <= $year_now; $year++) {

				$y_key = 'bez:report:year:'.$year;
				$data[$y_key] = array('id' => $y_key, 'type' => 'd', 'level' => 3, 'title' => $year);

				if (isset($this->value['year']) && (int)$this->value['year'] == $year) {
					$data['bez:report:year:'.$year]['open'] = true;

					if ($year == $year_now)
						$mon_max = $mon_now;
					else
						$mon_max = 12;
					for ( ; $mon <= $mon_max; $mon++) {
						$m_key = $y_key.':month:'.$mon;
						$data[$m_key] = array('id' => $m_key, 'type' => 'f', 'level' => 4,
						'title' => $mon < 10 ? '0'.$mon : $mon);
					}	
				}
				$mon = 1;
			}
		}
		
		$data['bez:activity_report'] = array('id' => 'bez:activity_report', 'type' => 'd', 'level' => 2, 'title' => $this->getLang('activity_report'));
		if ($this->value['bez'] == 'activity_report') {
			$data['bez:activity_report']['open'] = true;

			$oldest = $isso->get_oldest_close_date();
			$year_old = (int)date('Y', $oldest);
			$mon_old = (int)date('n', $oldest);

			$mon = $mon_old;
			for ($year = $year_old; $year <= $year_now; $year++) {

				$y_key = 'bez:activity_report:year:'.$year;
				$data[$y_key] = array('id' => $y_key, 'type' => 'd', 'level' => 3, 'title' => $year);

				if (isset($this->value['year']) && (int)$this->value['year'] == $year) {
					$data['bez:activity_report:year:'.$year]['open'] = true;

					if ($year == $year_now)
						$mon_max = $mon_now;
					else
						$mon_max = 12;
					for ( ; $mon <= $mon_max; $mon++) {
						$m_key = $y_key.':month:'.$mon;
						$data[$m_key] = array('id' => $m_key, 'type' => 'f', 'level' => 4,
						'title' => $mon < 10 ? '0'.$mon : $mon);
					}	
				}
				$mon = 1;
			}
		}



		if (isset($this->value['bez'])) {
			$data['bez:start']['open'] = true;
		} else {
			$data['bez:start']['open'] = false;
			array_splice($data, 1);
		}

		if ($helper->user_admin() && $data['bez:start']['open'] == true) {
			$data['bez:types'] = array('id' => 'bez:types', 'type' => 'f', 'level' => 2, 'title' =>
				$this->getLang('types_manage'));
			//~ $data['bez:root_causes'] = array('id' => 'bez:root_causes', 'type' => 'f', 'level' => 2, 'title' =>
				//~ $this->getLang('root_causes'));
			$data['bez:task_types'] = array('id' => 'bez:task_types', 'type' => 'f', 'level' => 2, 'title' =>
				$this->getLang('task_types'));
		}

        $R->doc .= '<div class="plugin__bez">';
        $R->doc .= html_buildlist($data,'idx',array($this,'_list'),array($this,'_li'));
        $R->doc .= '</div>';

		return true;
	}

	function _bezlink($id, $title) {
		//$uri = wl($id);
		$uri = DOKU_URL . 'doku.php?id='.$id;
		return '<a href="'.$uri.'">'.($title).'</a>';
	}

    function _list($item){

		$ex = explode(':', $item['id']);

		for ($i = 0; $i < count($ex); $i += 2)
			$item_value[urldecode($ex[$i])] = urldecode($ex[$i+1]);

		//pola brane pod uwagę przy określaniu aktualnej strony
		$fields = array('bez', 'tid', 'cid', 'tasktype', 'taskstate');
		if ($item_value['bez'] == 'report' || $item_value['bez'] == 'activity_report') {
			$fields[] = 'month';
			$fields[] = 'year';
		}
//		if ($this->value[bez] == 'task_form' && isset($this->value[cid]))
//			unset($fields[0]);
		
		$actual_page = true;
		foreach ($fields as $field)
			if ($item_value[$field] != $this->value[$field])
				$actual_page = false;
				
		//specjalny hak dla zadań, boję się ruszać całej procedury
		if ($item_value['bez'] == 'issue_task' ||
			$item_value['bez'] == 'issue_cause_task' ||
			$item_value['bez'] == 'task_form' ||
			$item_value['bez'] == 'show_task')
				if ($item_value['tid'] == $this->value['tid'])
					$actual_page = true;
			
        if(($item['type'] == 'd' && $item['open']) ||  $actual_page) {
			$id = $item['id'];
			if ($this->lang_code != $this->default_lang)
				$id = $this->lang_code.':'.$id;
            return '<strong>'.$this->_bezlink($id, $item['title']).'</strong>';
        }else{
			$id = $item['id'];
			if ($this->lang_code != $this->default_lang)
				$id = $this->lang_code.':'.$id;
            return $this->_bezlink($id, $item['title']);
        }

    }

    function _li($item){
        if($item['type'] == "f"){
            return '<li class="level'.$item['level'].'">';
        }elseif($item['open']){
            return '<li class="open">';
        }else{
            return '<li class="closed">';
        }
    }
}
