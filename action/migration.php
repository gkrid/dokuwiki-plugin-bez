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
//        $controller->register_hook('PLUGIN_SQLITE_DATABASE_UPGRADE', 'AFTER', $this, 'handle_migrations_after');
    }

    /**
     * Call our custom migrations when defined
     *
     * @param Doku_Event $event
     * @param $param
     */
    public function handle_migrations(Doku_Event $event, $param) {
        if ($event->data['sqlite']->getAdapter()->getDbName() !== 'b3p') {
            return;
        }
        $to = $event->data['to'];

        if(is_callable(array($this, "migration$to"))) {
            $event->preventDefault();
            $event->result = call_user_func(array($this, "migration$to"), $event->data);
        }
    }

    /**
     * Call our custom migrations when defined
     *
     * @param Doku_Event $event
     * @param $param
     */
    public function handle_migrations_after(Doku_Event $event, $param) {
        if ($event->data['sqlite']->getAdapter()->getDbName() !== 'b3p') {
            return;
        }
        $to = $event->data['to'];

        if(is_callable(array($this, "migration$to"))) {
            $event->result = call_user_func(array($this, "migration$to"), $event->data);
        }
    }

    protected function migration12($data) {
        global $INFO;

        $file = $data['file'];
        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = $data['sqlite'];

        $sql = file_get_contents($file);
        if($sql === false) {
            throw new Exception('cannot open file ' . $file);
        }

        $matches = array();
        preg_match_all('/.*?(?(?=BEGIN)BEGIN.*?END)\s*;/is', $sql, $matches);
        $queries = $matches[0];

        $db = $sqlite->getAdapter()->getPdo();

//        $db->beginTransaction();   // translation already started
        foreach($queries as $query) {
            $res = $db->query($query);
            if($res === false) {
                $err = $db->errorInfo();
                msg($err[0] . ' ' . $err[1] . ' ' . $err[2] . ':<br /><pre>' . hsc($query) . '</pre>', -1);
                $db->rollBack();
                return false;
            }
        }
//        $db->commit();  // commit will be done inside SQLiteDB

        return true;
    }

    protected function migration3($data) {
        global $INFO;

        $file = $data['file'];
        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = $data['sqlite'];

        $sql = file_get_contents($file);
        if($sql === false) {
            throw new Exception('cannot open file ' . $file);
        }

        $matches = array();
        preg_match_all('/.*?(?(?=BEGIN)BEGIN.*?END)\s*;/is', $sql, $matches);
        $queries = $matches[0];

        $db = $sqlite->getAdapter()->getPdo();

//        $db->beginTransaction();  // translation already started
        foreach($queries as $query) {
            $res = $db->query($query);
            if($res === false) {
                $err = $db->errorInfo();
                msg($err[0] . ' ' . $err[1] . ' ' . $err[2] . ':<br /><pre>' . hsc($query) . '</pre>', -1);
                $db->rollBack();
                return false;
            }
        }
//        $db->commit();  // commit will be done inside SQLiteDB

        return true;
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
        global $INFO;

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

        $db = $sqlite->getAdapter()->getPdo();

//        $db->beginTransaction(); // translation already started
        foreach ($queries as $query) {
            $res = $db->query($query);
            if($res === false) {
                $err = $db->errorInfo();
                msg($err[0].' '.$err[1].' '.$err[2].':<br /><pre>'.hsc($query).'</pre>', -1);
                $db->rollBack();
                return false;
            }
        }

        $bez_file = DOKU_INC . 'data/meta/bez.sqlite3';
        if (!file_exists($bez_file)) {
//        $db->commit();  // commit will be done inside SQLiteDB
            return true;
        }

        //import from bez
        $bez = new \PDO('sqlite:' . $bez_file);

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $bez->query('SELECT * FROM issuetypes');
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $sqlite->storeEntry('label', array('id' => $row['id'],
                                                            'name' => $row['pl'],
                                                            'added_by' => $INFO['client'],
                                                            'added_date' => date('c')));
        }

        $stmt = $bez->query('SELECT * FROM tasktypes');
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $sqlite->storeEntry('task_program', array('id' => $row['id'],
                                                             'name' => $row['pl'],
                                                             'added_by' => $INFO['client'],
                                                             'added_date' => date('c')));
        }

        $stmt = $bez->query('SELECT *, (SELECT COUNT(*) FROM tasks
								WHERE tasks.cause = commcauses.id) AS task_count FROM commcauses');
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['type'] == '0') {
                $type = 'comment';
            } elseif ($row['type'] == '1') {
                $type = 'cause_real';
            } elseif ($row['type'] == '2') {
                $type = 'cause_potential';
            }
            $sqlite->storeEntry('thread_comment',
                                       array('id' => $row['id'],
                                             'thread_id' => $row['issue'],
                                             'type' => $type,
                                             'author' => $row['reporter'],
                                             'create_date' => date('c', strtotime($row['datetime'])),
                                             'last_modification_date' => date('c', strtotime($row['datetime'])),
                                             'content' => $row['content'],
                                             'content_html' => $row['content_cache'],
                                             'task_count' => $row['task_count']));
        }

        $stmt = $bez->query('SELECT tasks.*, commcauses.type AS cause_type
                                          FROM tasks
                                          LEFT JOIN commcauses ON tasks.cause = commcauses.id');
        //thread_id => array('user_id' => 'user_id')
        $task_assignee = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if ($row['close_date'] != '') {
                $last_mod = date('c', (int) $row['close_date']);
            } else {
                $last_mod = date('c', (int) $row['date']);
            }

            $data =  array('id' => $row['id'],
                           'original_poster' => $row['reporter'],
                           'assignee' => $row['executor'],
                           'create_date' => date('c', (int) $row['date']),
                           'last_activity_date' => $last_mod,
                           'last_modification_date' => $last_mod,
                           'plan_date' => $row['plan_date'],
                           'all_day_event' => $row['all_day_event'],
                           'start_time' => $row['start_time'],
                           'finish_time' => $row['finish_time'],
                           'content' => $row['task'],
                           'content_html' => $row['task_cache'],
                           'thread_id' => $row['issue'],
                           'thread_comment_id' => $row['cause'],
                           'task_program_id' => $row['tasktype']
            );

            if ($data['thread_id'] != '') {
                if (!is_array($task_assignee[$data['thread_id']])) {
                    $task_assignee[$data['thread_id']] = array();
                }
                $task_assignee[$data['thread_id']][$row['executor']] = $row['executor'];
            }

            if ($row['cost'] != '0') {
                $data['cost'] = $row['cost'];
            }

            if ($row['issue'] == '') {
                $data['type'] = 'program';
            } elseif ($row['cause'] == '') {
                $data['type'] = 'correction';
            } elseif ($row['cause_type'] == '1') {
                $data['type'] = 'corrective';
            } else {
                $data['type'] = 'preventive';
            }

            if ($row['state'] == '0') {
                $data['state'] = 'opened';
            } elseif ($row['state'] == '1' || $row['state'] == '2') {
                $data['state'] = 'done';
                $data['closed_by'] = $row['executor'];
                $data['close_date'] = date('c', (int) $row['close_date']);

                if ($row['reason'] != '') {
                    $sqlite->storeEntry('task_comment',
                                               array('task_id' => $row['id'],
                                                     'author' => $row['executor'],
                                                     'create_date' => $data['close_date'],
                                                     'last_modification_date' => $data['close_date'],
                                                     'content' => $row['reason'],
                                                     'content_html' => $row['reason_cache']));
                }
            }

            //user_id => array()
            $participants = array();
            $subscribents = explode(',', $row['subscribents']);
            foreach ($subscribents as $user_id) {
                $participants[$user_id] = array('user_id' => $user_id, 'subscribent' => '1');
            }

            $op = $data['original_poster'];
            if (!isset($participants[$op])) {
                $participants[$op] = array('user_id' => $op, 'original_poster' => '1');
            } else {
                $participants[$op]['original_poster'] = '1';
            }

            $as = $data['assignee'];
            if (!isset($participants[$as])) {
                $participants[$as] = array('user_id' => $as, 'assignee' => '1');
            } else {
                $participants[$as]['assignee'] = '1';
            }

            foreach($participants as $part) {
                $part['task_id'] = $row['id'];
                $part['added_by'] = $INFO['client'];
                $part['added_date'] = date('c');

                $res = $sqlite->storeEntry('task_participant', $part);
                if ($res === false) {
                    throw new Exception($db->errorInfo());
                }
            }

            $sqlite->storeEntry('task', $data);
        }



        $stmt = $bez->query('SELECT *,
                                (SELECT COUNT(*) FROM tasks
								WHERE tasks.issue = issues.id) AS task_count,
								(SELECT COUNT(*) FROM tasks
								WHERE tasks.issue = issues.id AND tasks.state != 0) AS task_closed_count,
								(SELECT SUM(cost) FROM tasks
								WHERE tasks.issue = issues.id) AS task_sum_cost  
								FROM issues');

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

            $data =  array('id' => $row['id'],
                           'original_poster' => $row['reporter'],
                           'coordinator' => $row['coordinator'],
                           'create_date' => date('c', (int) $row['date']),
                           'last_activity_date' => date('c', strtotime($row['last_activity'])),
                           'last_modification_date' => date('c', (int) $row['last_mod']),
                           'title' => $row['title'],
                           'content' => $row['description'],
                           'content_html' => $row['description_cache'],
                           'task_count' => $row['task_count'],
                           'task_count_closed' => $row['task_closed_count']
            );

            if ($row['task_sum_cost'] != '0') {
                $data['task_sum_cost'] = $row['task_sum_cost'];
            }

            if ($row['coordinator'] == '') {
                $data['state'] = 'proposal';
            } elseif ($row['state'] == '1') {
                $data['closed_by'] = $row['coordinator'];
                $data['state'] = 'closed';
                $data['close_date'] = $data['last_modification_date'];

                $sqlite->storeEntry('thread_comment',
                                           array('thread_id' => $row['id'],
                                                 'type' => 'comment',
                                                 'author' => $row['coordinator'],
                                                 'create_date' => $data['close_date'],
                                                 'last_modification_date' => $data['close_date'],
                                                 'content' => $row['opinion'],
                                                 'content_html' => $row['opinion_cache']));


            } elseif ($row['state'] == '2') {
                $data['closed_by'] = $row['coordinator'];
                $data['state'] = 'rejected';
                $data['close_date'] = $data['last_modification_date'];

                if ($row['opinion'] != '') {
                    $res = $sqlite->storeEntry('thread_comment',
                                               array('thread_id' => $row['id'],
                                                     'type' => 'comment',
                                                     'author' => $row['coordinator'],
                                                     'create_date' => $data['close_date'],
                                                     'last_modification_date' => $data['close_date'],
                                                     'content' => $row['opinion'],
                                                     'content_html' => $row['opinion_cache']));
                    if ($res === false) {
                        throw new Exception($db->errorInfo());
                    }
                }
            } else {
                $data['state'] = 'opened';
            }

            $sqlite->storeEntry('thread', $data);

            $sqlite->storeEntry('thread_label',
                                       array('thread_id' => $row['id'],
                                             'label_id' => $row['type']));

            //participants
            //user_id => array()
            $participants = array();
            $org_participants = array_filter(explode(',', $row['participants']));
            foreach ($org_participants as $user_id) {
                $participants[$user_id] = array('user_id' => $user_id);
            }

            $subscribents = array_filter(explode(',', $row['subscribents']));
            foreach ($subscribents as $user_id) {
                if (!isset($participants[$user_id])) {
                    $participants[$user_id] = array('user_id' => $user_id);
                }
                $participants[$user_id]['subscribent'] = '1';
            }

            $stmt_i = $bez->query('SELECT reporter FROM commcauses WHERE issue=' . $row['id']);
            while ($commcause = $stmt_i->fetch(\PDO::FETCH_ASSOC)) {
                $user_id = $commcause['reporter'];
                if (!isset($participants[$user_id])) {
                    $participants[$user_id] = array('user_id' => $user_id);
                }
                $participants[$user_id]['commentator'] = '1';
            }

            if (is_array($task_assignee[$row['id']])) foreach ($task_assignee[$row['id']] as $user_id) {
                if (!isset($participants[$user_id])) {
                    $participants[$user_id] = array('user_id' => $user_id);
                }
                $participants[$user_id]['task_assignee'] = '1';
            }

            $op = $data['original_poster'];
            if (!isset($participants[$op])) {
                $participants[$op] = array('user_id' => $op, 'original_poster' => '1');
            } else {
                $participants[$op]['original_poster'] = '1';
            }

            $cor = $data['coordinator'];
            if (!isset($participants[$cor])) {
                $participants[$cor] = array('user_id' => $cor, 'coordinator' => '1');
            } else {
                $participants[$cor]['coordinator'] = '1';
            }

            foreach($participants as $part) {
                $part['thread_id'] = $row['id'];
                $part['added_by'] = $INFO['client'];
                $part['added_date'] = date('c');

                $sqlite->storeEntry('thread_participant', $part);
            }

        }

//        $db->commit();  // commit will be done inside SQLiteDB

        return true;
    }

    protected function migration8($data) {
        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = $data['sqlite'];

        $db = $sqlite->getAdapter()->getPdo();
        $db->commit(); // stop current transatction
        $db->query('PRAGMA journal_mode=WAL');
        $db->beginTransaction(); // recreate transaction
        return true;
    }

}
