<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
 
 // must be run within DokuWiki
if(!defined('DOKU_INC')) die();
 if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

class admin_plugin_bez_backup extends DokuWiki_Admin_Plugin {

 
	function getMenuText($language) {
		return 'Zarządzaj kopiami zapasowymi BEZ';
	}
	
	private $backup_dir;
	function __construct() {
		$this->backup_dir = DOKU_INC . 'data/bez-backups/';
	}
	function get_backup_list() {
		if (!file_exists($this->backup_dir))
    			mkdir($this->backup_dir, 0777, true);
    		
		$scanned_directory = array_diff(scandir($this->backup_dir), array('..', '.'));
		return $scanned_directory;
	}
	
	function create_backup($name='') {
		if ($name != '') $name = ".$name";
		copy(DOKU_INC . 'data/bez.sqlite', $this->backup_dir . "bez.sqlite$name." . time());
	}
	
	function restore_backup($name) {
		$this->crate_backup('before_restore');
		copy($this->backup_dir . $name, DOKU_INC . 'data/bez.sqlite');
	}
	
	/**
	 * handle user request
	 */
	function handle() {
	  return true;
	  if (count($_POST) == 0) return;   // first time - nothing to do
	  if (!checkSecurityToken()) return;
	  //importuj
	  if (isset($_POST['create'])) {
	  	$this->create_backup();
	  }
	}
	/**
	 * output appropriate html
	 */
	function html() {
		global $errors;
		ptln('<h1>Work in progress. It does not work yet.</h1>');
	  ptln('<h1>'.$this->getMenuText().'</h1>');
	 	ptln('<form action="'.wl($ID).'" method="post">');
	  // output hidden values to ensure dokuwiki will return back to this plugin
	  ptln('  <input type="hidden" name="do"   value="admin" />');
	  ptln('  <input type="hidden" name="page" value="bez_backup" />');
	  formSecurityToken();
	  ptln('<table>');
	  ptln('<tr><th colspan="2">Kopia</th></tr>');
	  foreach ($this->get_backup_list() as $backup) {
	  //<input type=submit value=Restore>
	  	ptln("<tr><td>$backup</td><td></td></tr>");
	  }
	  ptln('</table>');
	  ptln('  <input type="submit" name="create"  value="Utwórz" />');
	  ptln('</form>');
	}
 
}

