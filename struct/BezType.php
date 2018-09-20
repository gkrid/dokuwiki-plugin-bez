<?php

namespace dokuwiki\plugin\bez\struct;

use dokuwiki\plugin\struct\meta\ValidationException;
use dokuwiki\plugin\struct\types\AbstractMultiBaseType;

class BezType extends AbstractMultiBaseType {

    const BEZ_TABLE_CODES = array(
        '' => 'thread',
        'k' => 'thread_comment',
        'z' => 'task',
        'zk' => 'task_comment'
    );

    protected $config = array(
        'autocomplete' => array(
            'maxresult' => 10
        ),
    );

    /**
     * Output the stored data
     *
     * @param string $value the value stored in the database
     * @param \Doku_Renderer $R the renderer currently used to render the data
     * @param string $mode The mode the output is rendered in (eg. XHTML)
     * @return bool true if $mode could be satisfied
     */
    public function renderValue($value, \Doku_Renderer $R, $mode) {
        preg_match('/#([a-z]*)([0-9]+)/', $value, $matches);
        list(,$code, $id) = $matches;
        $title = $value;

        $anchor = '';
        $id_key = 'id';
        switch ($code) {
            case '':
                $table = 'thread';
                break;
            case 'k':
                $table = 'thread';
                $anchor = '#k' . $id;

                /** @var helper_plugin_sqlite $sqlite */
                $sqlite = plugin_load('helper', 'bez_db')->getDB();
                $res = $sqlite->query("SELECT thread_id FROM thread_comment WHERE id=?", $id);
                $id = $res->fetchColumn();
                break;
            case 'z':
                $table = 'task';
                $id_key = 'tid';
                break;
            case 'zk':
                $table = 'task';
                $id_key = 'tid';
                $anchor = '#zk' . $id;

                /** @var helper_plugin_sqlite $sqlite */
                $sqlite = plugin_load('helper', 'bez_db')->getDB();
                $res = $sqlite->query("SELECT task_id FROM task_comment WHERE id=?", $id);
                $id = $res->fetchColumn();
                break;
        }

        $wl = wl("bez:$table:$id_key:$id") . $anchor;
        $R->doc .= '<a href="'.$wl.'">'.$title.'</a>';


        return true;
    }

    /**
     * Cleans the link
     *
     * @param string $rawvalue
     * @return string
     */
    public function validate($rawvalue) {
        preg_match('/#([a-z]*)([0-9]+)/', $rawvalue, $matches);
        list(,$code, $id) = $matches;

        if (!is_numeric($id)) {
            throw new ValidationException('Invalid BEZ reference');
        }

        if (!in_array($code, array_keys(self::BEZ_TABLE_CODES))) {
                throw new ValidationException('Invalid BEZ reference');
        }

        $table = self::BEZ_TABLE_CODES[$code];

        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = plugin_load('helper', 'bez_db')->getDB();
        $res = $sqlite->query("SELECT COUNT(*) FROM $table WHERE id=?", $id);
        $count = $res->fetchColumn();
        if ($count == 0) {
            throw new ValidationException(ucfirst($table) . " with id: $id doesn't exists.");
        }

        return $rawvalue;
    }

    /**
     * Autocompletion support for pages
     *
     * @return array
     */
    public function handleAjax() {
        global $INPUT;

        // check minimum length
        $lookup = trim($INPUT->str('search'));
        if(utf8_strlen($lookup) < 1) return array();
        if ($lookup[0] != '#') return array();

        preg_match('/#([a-z]*)([0-9]+)/', $lookup, $matches);
        list(,$code, $id) = $matches;

        if (!is_numeric($id)) return array();
        if (!in_array($code, array_keys(self::BEZ_TABLE_CODES))) return array();

        $table = self::BEZ_TABLE_CODES[$code];

        // results wanted?
        $max = (int)$this->config['autocomplete']['maxresult'];
        if($max <= 0) return array();

        $bez_db_helper = plugin_load('helper', 'bez_db');

        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = $bez_db_helper->getDB();

        $fields = array('id');
        if ($table == 'thread' || $table == 'task') {
            $fields[] = 'state';
        }

        $sql = "SELECT " . implode(',', $fields) . " FROM $table WHERE id LIKE ? LIMIT $max";
        $res = $sqlite->query($sql, $id . '%');

        $result = array();
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {

            $name = $value = '#' . $code . $row['id'];
            if ($table == 'thread' || $table == 'task') {
                $state = $bez_db_helper->getLang('state_' . $row['state']);
                $name .= ' (' . $state . ')';
            }

            $result[] = array(
                'label' => $name,
                'value' => $value
            );
        }

        return $result;
    }
}