<?php

use \dokuwiki\plugin\bez;

// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_bez_nav extends DokuWiki_Syntax_Plugin {

    public function getPType() { return 'block'; }
    public function getType() { return 'substition'; }
    public function getSort() { return 99; }


    public function connectTo($mode) {
		$this->Lexer->addSpecialPattern('~~BEZNAV~~',$mode,'plugin_bez_nav');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
		return true;
    }

    public function render($mode, Doku_Renderer $r, $data) {
        global $auth, $INFO, $conf;
        if ($mode != 'xhtml') return;

        $r->info['cache'] = false;

        $r->doc .= '<nav id="plugin__bez">';
        $r->doc .= '<div style="background-color: #eee; color: #333; padding: 0 .3em;">' .
            inlineSVG(DOKU_PLUGIN . 'bez/images/logo.svg') .
            $this->getLang('bez') .
            '</div>';
        $r->doc .= '<ul>';
        $actions = array(
            'start' => $this->getLang('nav my_activities'),
            'threads' => $this->getLang('issues'),
            //'projects' => $this->getLang('nav projects'),
            'tasks' => $this->getLang('tasks'),
            'activity' => $this->getLang('activity_report')
        );
        /** @var bez\mdl\Model $model */
        $model = new bez\mdl\Model($auth, $INFO['client'], $this, $conf);
        if ($model->acl->get_level() >= BEZ_AUTH_LEADER) {
            $actions['types'] = $this->getLang('types_manage');
            $actions['task_programs'] = $this->getLang('task_types');
        }

        foreach ($actions as $action => $label) {
            $r->doc .= $this->_list($action, $label);
        }
        $r->doc .= '</ul>';
        $r->doc .= '</nav>';
    }

    protected function _list($action, $label) {
        global $INFO;

        $matches = array();
        preg_match('/bez:([a-z_]*)/i', $INFO['id'], $matches);
        $cur_action = '';
        if (isset($matches[1])) {
            $cur_action = $matches[1];
        }

        $ret = '<li>';
        if ($cur_action == $action) $ret .= '<strong>';
        $ret .= '<a href="' . action_plugin_bez_default::url($action) . '">' . $label . '</a>';
        if ($cur_action == $action) $ret .= '</strong>';
        $ret .= '</li>';

        return $ret;
    }
}
