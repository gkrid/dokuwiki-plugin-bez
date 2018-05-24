<?php

use \dokuwiki\plugin\bez;

class action_plugin_bez_base extends DokuWiki_Action_Plugin {

    /** @var  bez\mdl\Model */
    protected $model;

    /** @var  bez\meta\Tpl */
    protected $tpl;


    public function getPluginName() {
        return 'bez';
    }

    public function register(Doku_Event_Handler $controller) {
    }

    public function getGlobalConf($key='') {
        global $conf;
        if ($key == '') {
            return $conf;
        }
        return $conf[$key];
    }

    public function get_model() {
        return $this->model;
    }

    public function get_client() {
        global $INFO;
        return $INFO['client'];
    }

    public function get_tpl() {
        return $this->tpl;
    }

    public function get_level() {
        return $this->model->get_level();
    }

    public function bez_tpl_include($tpl_file='', $return=false) {
        $file = DOKU_PLUGIN . "bez/tpl/$tpl_file.php";
        if (!file_exists($file)) {
            throw new Exception("$file doesn't exist");
        }

        $tpl = $this->tpl;
        if ($return) ob_start();
        include $file;
        if ($return) return ob_get_clean();

    }

    public function createObjects() {
        global $auth;
        global $INFO;

        $this->model = new bez\mdl\Model($auth, $INFO['client'], $this);
        $this->tpl = new bez\meta\Tpl($this);
    }

    public function id() {
        $args = func_get_args();

        if (count($args) === 0) {
            return $_GET['id'];
        }

        $elms = array();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $k => $v) {
                    $elms[] = $k;
                    $elms[] = $v;
                }
            } else {
                $elms[] = $arg;
            }
        }
        array_unshift($elms, 'bez');


        if ($this->getGlobalConf('lang') != '') {
            array_unshift($elms, $this->getGlobalConf('lang'));
        }

        return implode(':', $elms);
    }

    public function url() {
        $args = func_get_args();
        if (count($args) > 0) {
            $id = call_user_func_array(array($this, 'id'), $args);
            return DOKU_URL . 'doku.php?id=' . $id;
        }
    }
}