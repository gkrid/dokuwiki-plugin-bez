<?php
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bez_qlink extends DokuWiki_Syntax_Plugin {

    function getType() { return 'substition'; }
    function getSort() { return 34; }
	function connectTo($mode) {
		$this->Lexer->addSpecialPattern('#[z]?[0-9]+',$mode,'plugin_bez_qlink');
	}


    function handle($match, $state, $pos, Doku_Handler $handler) {
		$code = substr($match, 1, 1);
		if ($code === 'z') {
			$nr = substr($match, 2, strlen($match));
			return array('z', $nr);
		} else {
			$nr = substr($match, 1, strlen($match));
			return array('', $nr);
		}
    }

    function render($mode, Doku_Renderer $renderer, $link) {
		if ($mode == 'xhtml') {
			$id = $_GET['id'];
			$ex = explode(':', $id);
			
			$lang_code = '';
			/*english namespace*/
			switch($ex[0]) {
				case 'en':
					$lang_code = $ex[0].':';
			}
			$nr = $link[1];
			if ($link[0] === 'z') {
				$renderer->doc .= '<a href="?id='.$lang_code.'bez:task:tid:'.$nr.'">#z'.$nr.'</a>';
			} else {
				$renderer->doc .= '<a href="?id='.$lang_code.'bez:thread:id:'.$nr.'">#'.$nr.'</a>';
			}
			return true;
		}
		return false;
    }
}
