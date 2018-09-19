<?php

namespace dokuwiki\plugin\bez\struct;

use dokuwiki\plugin\struct\meta\ValidationException;
use dokuwiki\plugin\struct\types\AbstractMultiBaseType;

class BezType extends AbstractMultiBaseType {

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
        $title = $value;
        $id = substr($value, 1);//remove #
        if ($id[0] == 'z') {
            $id = substr($id, 1);
            $wl = wl("bez:task:tid:$id");
        } else {
            $wl = wl("bez:thread:id:$id");
        }

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
        $value = trim($rawvalue);

        $id = substr($value, 1);//remove #
        if ($id[0] == 'z') {
            $id = substr($id, 1);
            $table = 'task';
        } else {
            $table = 'thread';
        }

        if (!is_numeric($id)) {
            throw new ValidationException('Invalid BEZ reference');
        }


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

        $id = substr($lookup, 1);
        if ($id[0] == 'z') {
            $id = substr($id, 1);
            $table = 'task';
        } else {
            $table = 'thread';
        }

        if (!is_numeric($id)) return array();


        // results wanted?
        $max = (int)$this->config['autocomplete']['maxresult'];
        if($max <= 0) return array();

        $bez_db_helper = plugin_load('helper', 'bez_db');

        /** @var helper_plugin_sqlite $sqlite */
        $sqlite = $bez_db_helper->getDB();
        $sql = "SELECT id, state FROM $table WHERE id LIKE ? LIMIT $max";
        $res = $sqlite->query($sql, $id . '%');

        $result = array();
        while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
            $state = $bez_db_helper->getLang('state_' . $row['state']);

            $name = $row['id'] . ' (' . $state . ')';
            if ($table == 'task') {
                $name = '#z' . $name;
                $value = '#z' . $row['id'];
            } else {
                $name = '#' . $name;
                $value = '#' . $row['id'];
            }

            $result[] = array(
                'label' => $name,
                'value' => $value
            );
        }

        return $result;
    }
}