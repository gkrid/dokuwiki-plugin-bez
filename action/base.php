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

    public function createObjects($skip_acl=false) {
        global $auth;
        global $INFO;

        if ($skip_acl) {
            $client = false;
        } else {
            $client = $INFO['client'];
        }

        $this->model = new bez\mdl\Model($auth, $client, $this, $skip_acl);
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
                    //replace special chars
                    list($k, $v) = str_replace(array(':', '#'), '', array($k, $v));
                    //don't create id with empty value
                    if (empty($k) || empty($v)) {
                        continue;
                    }
                    $elms[] = $k;
                    $elms[] = $v;
                }
            } else {
                $elms[] = $arg;
            }
        }
        array_unshift($elms, 'bez');


        //pl is default language
        if ($this->getGlobalConf('lang') != '' && $this->getGlobalConf('lang') != 'pl') {
            array_unshift($elms, $this->getGlobalConf('lang'));
        }

//        $elms = array_map(function ($elm) {
//            return str_replace(array(':', '#'), '', $elm);
//        }, $elms);
        return implode(':', $elms);
    }

    public function url() {
        global $conf;

        $args = func_get_args();
        if (count($args) > 0) {
            if (isset($args[count($args)-1]['GET'])) {
                $get = array_pop($args)['GET'];
                $get = http_build_query($get);
            }
            $id = call_user_func_array(array($this, 'id'), $args);

            if ($conf['userewrite'] == '1') {
                if ($get) $get = "?$get";
                return DOKU_URL . $id. $get;
            } elseif ($conf['userewrite'] == '2') {
                if ($get) $get = "?$get";
                return DOKU_URL . 'doku.php/' . $id . $get;
            } else {
                if ($get) $get = "&$get";
                return DOKU_URL . 'doku.php?id=' . $id . $get;
            }

        }
    }
}