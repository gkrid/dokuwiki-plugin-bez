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
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bez_nav extends DokuWiki_Syntax_Plugin {
	private $value = array();
	private $lang_code = '';
	private $default_lang = 'pl';

    function getPType() { return 'block'; }
    function getType() { return 'substition'; }
    function getSort() { return 99; }


    function connectTo($mode) {
		$this->Lexer->addSpecialPattern('~~BEZNAV~~',$mode,'plugin_bez_nav');
    }

	function __construct() {
		global $conf;

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
	}

    function handle($match, $state, $pos, &$handler)
    {
		return true;
    }

    function render($mode, &$R, $pass) {
		global $INFO;

		$helper = $this->loadHelper('bez');
		if ($mode != 'xhtml' || !$helper->user_viewer()) return false;

        $R->info['cache'] = false;

		$data = array(
			'bez:start' => array('id' => 'bez:start', 'type' => 'd', 'level' => 1, 'title' => $this->getLang('bez')),
		);

		if ($helper->user_editor())
			$data['bez:issue_report'] = array('id' => 'bez:issue_report', 'type' => 'f', 'level' => 2, 'title' => $this->getLang('bds_issue_report'));

		$data['bez:issues'] = array('id' => 'bez:issues:year:'.date('Y'), 'type' => 'd', 'level' => 2, 'title' => $this->getLang('bds_issues'));

		$task_pages = array('issue_tasks', 'task_form', 'issue_task');
		$cause_pages = array('issue_causes', 'issue_cause', 'cause_form', 'issue_cause_task');
		$issue_pages = array_merge(array('issue', 'rr', '8d'), $task_pages, $cause_pages);

		if (in_array($this->value['bez'], $issue_pages) || ($this->value[bez] == 'issue_report' && isset($this->value[id]))) {
			$data['bez:issues']['open'] = true;
			$id = (int)$this->value[id];

			$isso = new Issues();
			$issue_opened = $isso->opened($id);

			$tasko = new Tasks();
			$causo = new Causes();

			$pid = "bez:issue:id:$id";
			$data[$pid] = array('id' => $pid, 'type' => 'd', 'level' => 3,
												'title' => "#$id", 'open' => true);

			$pid = "bez:issue_tasks:id:$id";
			$data[$pid] = array('id' => $pid, 'type' => 'd', 'level' => 4,
								'title' => $this->getLang('correction_nav'));
			if (in_array($this->value['bez'], $task_pages) && $this->value[cid] == '') {
				$data[$pid][open] = true;

				if ($issue_opened) {
					$rpid = "bez:task_form:id:$id";
					$data[$rpid] = array('id' => $rpid, 'type' => 'f', 'level' => 5,
											'title' => $this->getLang('add_correction'));
				}
				$res = $tasko->get_filtered(array('issue' => $id, 'action' => '0'));
				foreach ($res as $r) {
					$rpid = "bez:issue_task:id:$id:tid:$r[id]";
					$data[$rpid] = array('id' => $rpid, 'type' => 'f', 'level' => 5,
										'title' => '#z'.$r[id]);
				}
			}

			$pid = "bez:issue_causes:id:$id";
			$data[$pid] = array('id' => $pid, 'type' => 'd', 'level' => 4,
								'title' => $this->getLang('causes'));
			if (in_array($this->value['bez'], $cause_pages) || $this->value[cid] != '') {
				$data[$pid][open] = true;
				if ($issue_opened) {
					$rpid = "bez:cause_form:id:$id";
					$data[$rpid] = array('id' => $rpid, 'type' => 'f', 'level' => 5,
											'title' => $this->getLang('add_cause'));
				}
				$res = $causo->get($id);
				foreach ($res as $r) {
					$rpid = "bez:issue_cause:id:$id:cid:$r[id]";
					$data[$rpid] = array('id' => $rpid, 'type' => 'd', 'level' => 5,
										'title' => '#p'.$r[id]);

					if ((int)$this->value['cid'] == $r[id]) {
						$data[$rpid][open] = true;

						$pres = $tasko->get($id, $r['id']);
						foreach ($pres as $pr) {
							$rptid = "bez:issue_cause_task:id:$id:cid:$r[id]:tid:$pr[id]";
							$data[$rptid] = array('id' => $rptid, 'type' => 'f', 'level' => 6,
										'title' => '#z'.$pr[id]);

						}
					}
				}
				
			}

		}
		$data['bez:tasks'] = array('id' => 'bez:tasks:year:'.date('Y'), 'type' => 'f', 'level' => 2, 'title' => $this->getLang('bez_tasks'));

		$data['bez:report_open'] = array('id' => 'bez:report_open', 'type' => 'd', 'level' => 2, 'title' => $this->getLang('report_open'));

		$isso = new Issues();
		$year_now = (int)date('Y');
		$mon_now = (int)date('n');

		if ($this->value['bez'] == 'report_open') {
			$data['bez:report_open']['open'] = true;

			$oldest = $isso->get_oldest_open_date();
			$year_old = (int)date('Y', $oldest);
			$mon_old = (int)date('n', $oldest);

			$mon = $mon_old;
			for ($year = $year_old; $year <= $year_now; $year++) {
				$y_key = 'bez:report_open:year:'.$year;
				$data[$y_key] = array('id' => $y_key, 'type' => 'd', 'level' => 3, 'title' => $year);

				if (isset($this->value['year']) && (int)$this->value['year'] == $year) {
					$data['bez:report_open:year:'.$year]['open'] = true;

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



		if (isset($this->value['bez'])) {
			$data['bez:start']['open'] = true;
		} else {
			$data['bez:start']['open'] = false;
			array_splice($data, 1);
		}

		if ($helper->user_admin() && $data['bez:start']['open'] == true)
			$data['bez:types'] = array('id' => 'bez:types', 'type' => 'f', 'level' => 2, 'title' => $this->getLang('types_manage'));

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
		$fields = array('bez', 'year', 'tid', 'cid');
		if ($item_value['bez'] == 'report' || $item_value['bez'] == 'report_open') {
			$fields[] = 'month';
			$fields[] = 'year';
		}
		if ($this->value[bez] == 'task_form' && isset($this->value[cid]))
			unset($fields[0]);

		$actual_page = true;
		foreach ($fields as $field)
			if ($item_value[$field] != $this->value[$field])
				$actual_page = false;



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
