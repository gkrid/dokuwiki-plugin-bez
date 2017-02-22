<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */

// must be run within DokuWiki
if (!defined('DOKU_INC')) die();

require_once DOKU_PLUGIN . 'bez/cron-functions.php';

class admin_plugin_bez_notifications extends DokuWiki_Admin_Plugin {
    
    
    function getMenuText($language) {
        return '[BEZ] Wyślij notyfikację';
    }
    
    /**
     * handle user request
     */
    private $output = array();
    function handle() {
        global $auth, $conf;
        
        //inicialize lang array
        $this->getLang('bez');
        
        if (count($_POST) === 0)
            return; // first time - nothing to do
        if (!checkSecurityToken())
            return;
        //importuj
        if (isset($_POST['send'])) {
            $http = 'http';
            if ($_SERVER['HTTPS'] === 'on') {
                $http = 'https';
            }
            $helper = $this->loadHelper('bez');
            $this->output = send_message($_SERVER['SERVER_NAME'], $http, $conf, $helper, $auth, $this->lang);
        }
    }
    /**
     * output appropriate html
     */
    function html() {
        ptln('<h1>' . $this->getMenuText('pl') . '</h1>');
        ptln('<form action="' . wl($ID) . '" method="post">');
        ptln('  <input type="hidden" name="do"   value="admin" />');
        ptln('  <input type="hidden" name="page" value="bez_notifications" />');
        formSecurityToken();
        ptln('  <input type="submit" name="send"  value="Wyślij powiadomienia" />');
        ptln('</form>');
        $log = array_reduce($this->output, function($carry, $item) {
            $carry .= htmlspecialchars($item) . "\n";
            return $carry;
        }, '');
        ptln('<pre style="margin-top: 10px;">');
        ptln($log);
        ptln('</pre>');
    }
    
}
