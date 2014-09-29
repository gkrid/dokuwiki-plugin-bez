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

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bez_nav extends DokuWiki_Syntax_Plugin {

    function getPType(){
       return 'block';
    }

    function getType() { return 'substition'; }
    function getSort() { return 99; }


    function connectTo($mode) {
	$this->Lexer->addSpecialPattern('~~BEZNAV~~',$mode,'plugin_bez_nav');
    }

    function handle($match, $state, $pos, &$handler)
    {
		return true;
    }

	private function user_can_edit() {
		global $INFO;
		global $auth;

		if ($auth->getUserData($INFO['client']) == true) {
			return true;
		} else {
			return false;
		}
	}
	private function user_can_view() {
		global $INFO;
		global $auth;

		if ($auth->getUserData($INFO['client']) == true) {
			return true;
		} else {
			return false;
		}
	}

    function render($mode, &$renderer, $data) {
		if($mode == 'xhtml') {

		if ( ! $this->user_can_view()) {
			return false;
		}

		$renderer->doc .= '<ul>';
		$renderer->doc .= '<li><a href="?id=bez:timeline">'.$this->getLang('bds_timeline').'</a></li>';
		$renderer->doc .= '<li><a href="?id=bez:issues">'.$this->getLang('bds_issues').'</a></li>';

		if ($this->user_can_edit()) {
			$renderer->doc .= '<li><a href="?id=bez:issue_report">'.$this->getLang('bds_issue_report').'</a></li>';
		}
		$renderer->doc .= '</ul>';

		}
	}
}
