<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Szymon Olewniczak <szymon.olewniczak@rid.pl>
 */

// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';
include_once DOKU_PLUGIN."bez/models/tasks.php";
include_once DOKU_PLUGIN."bez/models/issues.php";
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bez_nav extends DokuWiki_Syntax_Plugin {

    function getPType() { return 'block'; }
    function getType() { return 'substition'; }
    function getSort() { return 99; }


    function connectTo($mode) {
		$this->Lexer->addSpecialPattern('~~BEZNAV~~',$mode,'plugin_bez_nav');
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

		$isso = new Issues();
		$no = count($isso->get_close_issue());
		$title = str_replace('%d', $no, $this->getLang('menu_close_issue'));
		$data['bez:close_issue'] = array('id' => 'bez:close_issue', 'type' => 'f', 'level' => 2, 'title' => $title);

		$tasko = new Tasks();
		$no = count($tasko->get_close_task());
		$title = str_replace('%d', $no, $this->getLang('menu_close_task'));
		$data['bez:close_task'] = array('id' => 'bez:close_task', 'type' => 'f', 'level' => 2, 'title' => $title);

		$data['bez:issues'] = array('id' => 'bez:issues', 'type' => 'f', 'level' => 2, 'title' => $this->getLang('bds_issues'));

		if ($helper->user_admin())
			$data['bez:entity'] = array('id' => 'bez:entity', 'type' => 'f', 'level' => 2, 'title' => $this->getLang('entity_manage'));


		$id = $INFO['id'];
		$ex = explode(':', $id);
		$root = $ex[0];
		if ($root == 'bez') {
			$data['bez:start']['open'] = true;
		} else {
			$data['bez:start']['open'] = false;
			array_splice($data, 1);
		}

        $R->doc .= '<div class="plugin__bez">';
        $R->doc .= html_buildlist($data,'idx',array($this,'_list'),array($this,'_li'));
        $R->doc .= '</div>';

		return true;
	}

	function _bezlink($id, $title) {
		$uri = wl($id);
		return '<a href="'.$uri.'">'.($title).'</a>';
	}

    function _list($item){
        global $INFO;

        if(($item['type'] == 'd' && $item['open']) || $INFO['id'] == $item['id']){
            return '<strong>'.$this->_bezlink($item['id'], $item['title']).'</strong>';
        }else{
            return $this->_bezlink($item['id'], $item['title']);
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
