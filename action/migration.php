<?php
/**
 * DokuWiki Plugin bez (Action Component)
 *
 */

// must be run within Dokuwiki

if(!defined('DOKU_INC')) die();

/**
 * Class action_plugin_bez_migration
 *
 * Handle migrations that need more than just SQL
 */
class action_plugin_bez_migration extends DokuWiki_Action_Plugin {
    /**
     * @inheritDoc
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('PLUGIN_SQLITE_DATABASE_UPGRADE', 'BEFORE', $this, 'handle_migrations');
    }

    /**
     * Call our custom migrations when defined
     *
     * @param Doku_Event $event
     * @param $param
     */
    public function handle_migrations(Doku_Event $event, $param) {
        if ($event->data['sqlite']->getAdapter()->getDbname() !== 'b3p') {
            return;
        }
        $to = $event->data['to'];

        if(is_callable(array($this, "migration$to"))) {
            $event->preventDefault();
            $event->result = call_user_func(array($this, "migration$to"), $event->data);
        }
    }

    /**
     * Executes Migration 1
     *
     * Add a latest column to all existing multi tables
     *
     * @param helper_plugin_sqlite $sqlite
     * @return bool
     */
    protected function migration1($data) {
        $file = $data['file'];
        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = $data['sqlite'];

        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new Exception('cannot open file '.$file);
        }

        $matches = array();
        preg_match_all('/.*?(?(?=BEGIN)BEGIN.*?END)\s*;/is', $sql, $matches);
        $queries = $matches[0];

        $db = $sqlite->getAdapter()->getDb();

        $db->beginTransaction();
        foreach ($queries as $query) {
            $res = $db->query($query);
            if($res === false) {
                $err = $db->errorInfo();
                msg($err[0].' '.$err[1].' '.$err[2].':<br /><pre>'.hsc($sql).'</pre>', -1);
                $db->rollBack();
                return false;
            }
        }
        $db->commit();

        return true;
    }

}
