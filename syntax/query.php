<?php

// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_bez_query extends DokuWiki_Syntax_Plugin {

    public function getType() { return 'container'; }
    public function getAllowedTypes() {
        return array('container', 'formatting', 'substition', 'disabled');
    }
    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<bez-query.*?>(?=.*?</bez-query>)',$mode,'plugin_bez_query');
    }
    public function postConnect() {
        $this->Lexer->addExitPattern('</bez-query>','plugin_bez_query');
    }
    public function getSort() { return 34; }


    public function handle($match, $state, $pos, Doku_Handler $handler){
        switch ($state) {
          case DOKU_LEXER_ENTER :
                return array($state, $match);
 
          case DOKU_LEXER_UNMATCHED :  return array($state, $match);
          case DOKU_LEXER_EXIT :       return array($state, '');
        }
        return array();
    }

    public function render($mode, Doku_Renderer $renderer, $data) {
        // $data is what the function handle() return'ed.     
        if($mode == 'xhtml'){
            /** @var Doku_Renderer_xhtml $renderer */
            list($state,$match) = $data;
            switch ($state) {
                case DOKU_LEXER_ENTER :     
                    
                    $renderer->doc .= "QUERY: ".htmlspecialchars($match)."";
                    $renderer->doc .= "<pre>"; 
                    break;
 
                case DOKU_LEXER_UNMATCHED :  
                    $renderer->doc .= $renderer->_xmlEntities($match); 
                    break;
                case DOKU_LEXER_EXIT :       
                    $renderer->doc .= "</pre>"; 
                    break;
            }
            return true;
        }
        return false;
    }
}
