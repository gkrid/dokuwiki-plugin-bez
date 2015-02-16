<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 */
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
		$this->Lexer->addSpecialPattern('#[0-9]+',$mode,'plugin_bez_qlink');
	}


    function handle($match, $state, $pos, &$handler) {
		$nr = substr($match, 1, strlen($match));
		return $nr;
    }

    function render($mode, &$renderer, $nr) {
		if ($mode == 'xhtml') {
			$id = $_GET['id'];
			$ex = explode(':', $id);
			
			$lang_code = '';
			/*english namespace*/
			switch($ex[0]) {
				case 'en':
					$lang_code = $ex[0].':';
			}
			$renderer->doc .= '<a href="?id='.$lang_code.'bez:issue_show:'.$nr.'">#'.$nr.'</a>';
			return true;
		}
		return false;
    }
}
