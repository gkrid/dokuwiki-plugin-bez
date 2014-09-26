<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Szymon Olewniczak <szymon.olewniczak@rid.pl>
 */
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bds_qlink extends DokuWiki_Syntax_Plugin {

    function getType() { return 'substition'; }
    function getSort() { return 34; }

	function connectTo($mode) {
		$this->Lexer->addSpecialPattern('#[0-9]+:?[0-9]*',$mode,'plugin_bds_qlink');
	}


    function handle($match, $state, $pos, &$handler) {
		$whash = substr($match, 1, strlen($match));
		$ex = explode(':', $whash);
		return array($ex[0], isset($ex[1]) ? $ex[1] : 0);
    }

    function render($mode, &$renderer, $data) {
		if ($mode == 'xhtml') {
			list($issue, $event) = $data;

			$bds = $this->loadHelper('bds');
			$renderer->doc .= $bds->html_anchor_to_event($issue, $event, true);

			return true;
		}
		return false;
    }
}
