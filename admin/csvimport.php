<?php
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
$errors = array();
include_once DOKU_PLUGIN."bez/models/tasks.php";
class admin_plugin_bez_csvimport extends DokuWiki_Admin_Plugin {

	private $imported = false;
 
	function getMenuText($lang) {
		return 'BEZ - zaimportuj dane historyczne';
	}
 
	/**
	 * handle user request
	 */
	function handle() {
		global $errors;
 
		if (!isset($_REQUEST['run'])) return;   // first time - nothing to do
		if (!checkSecurityToken()) return;

		//importuj
		$csv = $_POST['bez_data'];

		$lines = explode("\n", $csv);
		/*usuń nagłówek*/
		array_shift($lines);
		
		
		$data = array();
		foreach($lines as $line) $data[] = str_getcsv($line); //parse the items in rows 


		$tasko = new Tasks();
		foreach ($data as $row) {
			$tasko->errinsert(array(
				'task' => $row[0],
				'state' => $row[1],
				'executor' => $row[2],
				'cost' => $row[3],
				'reason' => $row[4],
				'reporter' => $row[5],
				'date' => $row[6] == '' ? '' : strtotime($row[6]),
				'close_date' => $row[7] == '' ? '' : strtotime($row[7]),
				'cause' => $row[8],
				'plan_date' => $row[9],
				'all_day_event' => $row[10],
				'start_time' => $row[11],
				'finish_time' => $row[12],
				'issue' => $row[13]
			), 'tasks');
		}
		if (count($errors) == 0) {
			$this->imported = true;
		}
	}
 
	/**
	 * output appropriate html
	 */
	function html() {
		global $errors;
		ptln('<h1>'.$this->getMenuText('pl').'</h1>');
		if ($this->imported == true) {
		    ptln('<div class="success">Dane zostały zaimportowane pomyślnie.</div>');
		} else {
		  	if (is_array($errors))
		  		foreach ($errors as $error) {
		  			echo '<div class="error">';
		  			echo $error;
		  			echo '</div>';
		  		}
		}
	 
		ptln('<form action="'.wl($ID).'" method="post">');
		// output hidden values to ensure dokuwiki will return back to this plugin
		ptln('<input type="hidden" name="do"   value="admin" />');
		ptln('<input type="hidden" name="page" value="bez_csvimport" />');
		formSecurityToken();
		ptln('<label for="proza_data">Pierwsze pole jest polem nagłówka!<br>Kolejność pól: <i>Zadanie, Status, Wykonawca, Koszt, Przyczyna (zamknięcia/odrzucenia), Zgłaszający, Data zgłoszenia, Data Zamknięcia, Id przyczyny, Planowana data wykonania, Zdarzenie całodniowe (0/1), Godzina rozpoczęcia, Godzina Zakończenia, Id problemu</i><br />');
		ptln('<i>status ∊ {0,1,2}</i>, gdzie 0 - otwarte, 1 - zamknięte, 2 - odrzucone, <br />');
		ptln('<i>Wykonawca i Zgłaszający</i> musi być poprawnym nickiem użytkownika wiki.<br />');
		ptln('Wszystkie daty muszą być podane w formacie: YYYY-MM-DD<br />');
		ptln('Wszystkie godziny muszą być podane w formacie: HH:MM<br />');
		ptln('Dane w wormacie CSV do zaimportowania:<br>Separator: <b>,</b><br />Separator tekstu: <b>"</b><br />');
		
		ptln('<a href="lib/plugins/bez/admin/csvimport-example.csv">Przykładowy plik CSV</a><br /></label>');
		
		ptln('<textarea id="proza_data" name="bez_data" cols="100" rows="30"></textarea><br />');
		ptln('<input type="submit" name="run"  value="Importuj" />');
		ptln('</form>');
	}
}

