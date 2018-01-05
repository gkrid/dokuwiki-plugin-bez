<?php

namespace dokuwiki\plugin\bez\mdl;

class Task_comment extends Entity {

    //real
    protected $id, $task_id, $author, $create_date, $last_modification_date, $content, $content_html;

    /** @var Task */
    protected  $task;

    public static function get_columns() {
        return array('id', 'task_id', 'author',
                     'create_date', 'last_modification_date', 'content', 'content_html');
    }

    public function __get($property) {
        if ($property == 'task') {
            if ($this->task_id == null) {
                return null;
            }
            if ($this->task == null) {
                $this->task = $this->model->taskFactory->get_one($this->task_id);
            }
            return $this->task;
        }
        return parent::__get($property);
    }

    public function __construct($model, $defaults=array()) {
        parent::__construct($model, $defaults);

        $this->validator->set_rules(array(
                                        'content' => array(array('length', 10000), 'NOT NULL')
                                    ));

        //new object
        if ($this->id === NULL) {

            $this->author = $this->model->user_nick;
            $this->create_date = date('c');
            $this->last_modification_date = $this->create_date;


            if (!isset($defaults['task'])) {
                throw new \Exception('$defaults[task] not set');
            }
            $this->task = $defaults['task'];
            $this->task_id = $this->task->id;

            //we can change our own comments
            if ($this->author == $this->model->user_nick || $this->model->get_level() >= BEZ_AUTH_LEADER) {
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
            }

        } else {
            if (isset($defaults['task']) && $this->task_id == $defaults['task']->id) {
                $this->task = $defaults['task'];
            }

            //we can change our own comments
            if ($this->author == $this->model->user_nick || $this->model->get_level() >= BEZ_AUTH_LEADER) {
                $this->acl->grant('id', BEZ_PERMISSION_DELETE);
                $this->acl->grant('content', BEZ_PERMISSION_CHANGE);
            }
        }

    }
    public function set_data($post) {
        parent::set_data($post);
        $this->content_html = p_render('xhtml',p_get_instructions($this->content), $ignore);
    }

    public function mail_notify_add() {
        $rep = array(
            'content' => $this->content,
            'content_html' => $this->content_html,
            'who' => $this->author,
            'when' => $this->create_date
        );

        $rep['action'] = $this->model->action->getLang('mail_comment_added');
        $rep['action_color'] = 'transparent';
        $rep['action_border_color'] = '#E5E5E5';

        //$this->thread->mail_notify($rep);
    }
}
