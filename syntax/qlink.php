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
		$this->Lexer->addSpecialPattern('#(?:z|zk|k)?[0-9]+',$mode,'plugin_bez_qlink');
	}


    function handle($match, $state, $pos, Doku_Handler $handler) {
        preg_match('/#([a-z]*)([0-9]+)/', $match, $matches);
        list(,$code, $id) = $matches;

        $anchor = '';
        $id_key = 'id';
        switch ($code) {
            case '':
                $table = 'thread';
                break;
            case 'k':
                $table = 'thread';
                $anchor = '#k' . $id;

                /** @var helper_plugin_sqlite $sqlite */
                $sqlite = plugin_load('helper', 'bez_db')->getDB();
                $res = $sqlite->query("SELECT thread_id FROM thread_comment WHERE id=?", $id);
                $id = $res->fetchColumn();
                break;
            case 'z':
                $table = 'task';
                $id_key = 'tid';
                break;
            case 'zk':
                $table = 'task';
                $id_key = 'tid';
                $anchor = '#zk' . $id;

                /** @var helper_plugin_sqlite $sqlite */
                $sqlite = plugin_load('helper', 'bez_db')->getDB();
                $res = $sqlite->query("SELECT task_id FROM task_comment WHERE id=?", $id);
                $id = $res->fetchColumn();
                break;
        }

        return array($match, $table, $id_key, $id, $anchor);
    }

    function render($mode, Doku_Renderer $renderer, $data) {
		if ($mode == 'xhtml') {
			$id = $_GET['id'];
			$ex = explode(':', $id);
			
			$lang_code = '';
			/*english namespace*/
			switch($ex[0]) {
				case 'en':
					$lang_code = $ex[0].':';
			}

            list($match, $table, $id_key, $id, $anchor) = $data;
            $renderer->doc .= '<a href="?id='.$lang_code.'bez:'.$table.':'.$id_key.':'.$id.$anchor.'">'.$match.'</a>';

			return true;
		}
		return false;
    }
}
