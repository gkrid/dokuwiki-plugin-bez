<?php

use \dokuwiki\plugin\bez;

class action_plugin_bez_base extends DokuWiki_Action_Plugin {

    /** @var  bez\mdl\Model */
    protected $model;

    /** @var  bez\meta\Tpl */
    protected $tpl;

    /**
     * See init.php:getBaseURL
     *
     * @return string
     */
    protected function basedir() {
        if(!empty($conf['basedir'])){
            $dir = $conf['basedir'];
        }elseif(substr($_SERVER['SCRIPT_NAME'],-4) == '.php'){
            $dir = dirname($_SERVER['SCRIPT_NAME']);
        }elseif(substr($_SERVER['PHP_SELF'],-4) == '.php'){
            $dir = dirname($_SERVER['PHP_SELF']);
        }elseif($_SERVER['DOCUMENT_ROOT'] && $_SERVER['SCRIPT_FILENAME']){
            $dir = preg_replace ('/^'.preg_quote($_SERVER['DOCUMENT_ROOT'],'/').'/','',
                                 $_SERVER['SCRIPT_FILENAME']);
            $dir = dirname('/'.$dir);
        }else{
            $dir = '.'; //probably wrong
        }

        $dir = str_replace('\\','/',$dir);             // bugfix for weird WIN behaviour
        $dir = preg_replace('#//+#','/',"/$dir/");     // ensure leading and trailing slashes

        //handle script in lib/exe dir
        $dir = preg_replace('!lib/exe/$!','',$dir);

        //handle script in lib/plugins dir
        $dir = preg_replace('!lib/plugins/.*$!','',$dir);

        return $dir;
    }

    /**
     * See init.php:getBaseURL
     *
     * @return string
     */
    protected function baseurl() {
        //split hostheader into host and port
        if(isset($_SERVER['HTTP_HOST'])){
            $parsed_host = parse_url('http://'.$_SERVER['HTTP_HOST']);
            $host = isset($parsed_host['host']) ? $parsed_host['host'] : null;
            $port = isset($parsed_host['port']) ? $parsed_host['port'] : null;
        }elseif(isset($_SERVER['SERVER_NAME'])){
            $parsed_host = parse_url('http://'.$_SERVER['SERVER_NAME']);
            $host = isset($parsed_host['host']) ? $parsed_host['host'] : null;
            $port = isset($parsed_host['port']) ? $parsed_host['port'] : null;
        }else{
            $host = php_uname('n');
            $port = '';
        }

        if(is_null($port)){
            $port = '';
        }

        if(!is_ssl()){
            $proto = 'http://';
            if ($port == '80') {
                $port = '';
            }
        }else{
            $proto = 'https://';
            if ($port == '443') {
                $port = '';
            }
        }

        if($port !== '') $port = ':'.$port;

        return $proto.$host.$port;
    }

    public function loadConfig() {
        global $conf;

        $update_config = false;
        if (empty($conf['basedir']) || empty($conf['baseurl'])) {
            $update_config = true;

            include DOKU_PLUGIN . 'config/settings/config.class.php';
            $datafile = DOKU_PLUGIN . 'config/settings/config.metadata.php';
            $configuration = new configuration($datafile);
        }

        if (empty($conf['basedir'])) {
            $basedir = $this->basedir();
            $configuration->setting['basedir']->update($basedir);
            $conf['basedir'] = $basedir;
        }

        if (empty($conf['baseurl'])) {
            $baseurl = $this->baseurl();
            $configuration->setting['baseurl']->update($baseurl);
            $conf['baseurl'] = $baseurl;
        }

        if ($update_config) {
            $configuration->save_settings('config');
        }
        parent::loadConfig();
    }


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

        $elms = array_map(function ($elm) {
            return str_replace(':', '', $elm);
        }, $elms);
        return implode(':', $elms);
    }

    public function url() {
        global $conf;

        $args = func_get_args();
        if (count($args) > 0) {
            $id = call_user_func_array(array($this, 'id'), $args);
            if ($conf['userewrite'] == '1') {
                return DOKU_URL . $id;
            } elseif ($conf['userewrite'] == '2') {
                return DOKU_URL . 'doku.php/' . $id;
            } else {
                return DOKU_URL . 'doku.php?id=' . $id;
            }

        }
    }
}