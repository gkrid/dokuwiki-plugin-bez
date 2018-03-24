<?php

namespace dokuwiki\plugin\bez\struct;

use dokuwiki\plugin\struct\meta\ValidationException;
use dokuwiki\plugin\struct\types\AbstractMultiBaseType;

class BezType extends AbstractMultiBaseType {
    /** @var \action_plugin_bez_base */
    protected $bez_action;

    protected $config = array(
        'autocomplete' => array(
            'maxresult' => 10
        ),
    );

    public function __construct($config = null, $label = '', $ismulti = false, $tid = 0) {
        parent::__construct($config, $label, $ismulti, $tid);


        $this->bez_action = new \action_plugin_bez_base();
        $this->bez_action->createObjects();
    }

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

        if (!$this->bez_action->get_model()->factory($table)->exists($id)) {
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

        // this basically duplicates what we do in ajax_qsearch()
        $q = "SELECT id, state FROM $table WHERE id LIKE :id LIMIT $max";
        $stmt = $this->bez_action->get_model()->db->prepare($q);
        $stmt->bindValue(':id', "{$id}%");
        $stmt->execute();

        $result = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $state = $this->bez_action->getLang('state_' . $row['state']);

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