<?php
/** @var action_plugin_bez $this */

use \dokuwiki\plugin\bez;

if ($this->model->get_level() < BEZ_AUTH_USER) {
    throw new bez\meta\PermissionDeniedException();
}

//activity

class Timeline {
    protected $timeline = array();

    protected function datetime($iso8601) {
        $timestamp = strtotime($iso8601);
        $date = date('Y-m-d', $timestamp);
        $time = date('H:m', $timestamp);

        return array($date, $time);
    }

    public function push($datetime, $type, $author, $entity) {
        list($date, $time) = $this->datetime($datetime);

        if (!isset($this->timeline[$date])) $timeline[$date] = array();
        $this->timeline[$date][] = array('time' => $time,
                                   'type' => $type,
                                   'author' => $author,
                                   'entity' => $entity);
    }

    public function get_assoc() {
        //sort dates, iso8601 can be compared as strings
        krsort($this->timeline);

        //sort times
        foreach ($this->timeline as &$elm) {
            usort($elm, function ($a, $b) {
                return -1 * strcmp($a['time'], $b['time']);
            });
        }
        return $this->timeline;
    }
}

$month_earlier = date('c', strtotime('-1 month'));
$filter = array();
$filter['create_date'] = array('>=', $month_earlier, array('date'));
$threads = $this->model->threadFactory->get_all($filter, 'create_date DESC');
$thread_comments = $this->model->thread_commentFactory->get_all($filter, 'create_date DESC');
$tasks = $this->model->taskFactory->get_all($filter, 'create_date DESC');
$task_comments = $this->model->task_commentFactory->get_all($filter, 'create_date DESC');

$timeline = new Timeline();
foreach ($threads as $thread) {
    if ($thread->acl_of('id') < BEZ_PERMISSION_VIEW) continue;

    $project = '';
    if ($thread->type == 'project') {
        $project = '_project';
    }

    if ($thread->state == 'proposal') {
        $timeline->push($thread->create_date, 'thread_proposal' . $project, $thread->original_poster, $thread);
    } else {
        $timeline->push($thread->create_date, 'thread_opened' . $project, $thread->coordinator, $thread);
    }

    if ($thread->state == 'done') {
        $timeline->push($thread->last_activity_date, 'thread_done' . $project, $thread->coordinator, $thread);
    } elseif ($thread->state == 'closed') {
        $timeline->push($thread->last_activity_date, 'thread_closed' . $project, $thread->coordinator, $thread);
    } elseif ($thread->state == 'rejected') {
        $timeline->push($thread->last_activity_date, 'thread_rejected' . $project, $thread->coordinator, $thread);
    }
}

foreach ($thread_comments as $thread_comment) {
    if ($thread_comment->thread->acl_of('id') < BEZ_PERMISSION_VIEW) continue;

    if ($thread_comment->type == 'comment') {
        $timeline->push($thread_comment->create_date, 'thread_comment_added', $thread_comment->author, $thread_comment);
    } else {
        $timeline->push($thread_comment->create_date, 'thread_comment_cause_added', $thread_comment->author, $thread_comment);
    }
}

foreach ($tasks as $task) {
    if ($task->acl_of('id') < BEZ_PERMISSION_VIEW) continue;

    $timeline->push($task->create_date, 'task_opened', $task->assignee, $task);

    if ($task->state == 'done') {
        $timeline->push($task->last_activity_date, 'task_done', $task->assignee, $task);
    }
}

foreach ($task_comments as $task_comment) {
    if ($task_comment->task->acl_of('id') < BEZ_PERMISSION_VIEW) continue;

    $timeline->push($task_comment->create_date, 'task_comment_added', $task_comment->author, $task_comment);
}

$this->tpl->set('timeline', $timeline->get_assoc());

$orderby = array('sort', 'priority DESC', 'create_date DESC');
$filter = array('state' => 'proposal');
$proposals = $this->model->threadFactory->get_all($filter, $orderby);
$this->tpl->set('proposals', $proposals);
$this->tpl->set('proposals_count', $this->model->threadFactory->count($filter));

$orderby = array('sort', 'priority DESC', 'create_date DESC');
$filter = array('state' => 'opened', 'coordinator' => $this->model->user_nick);
$my_threads = $this->model->threadFactory->get_all($filter, $orderby);
$this->tpl->set('my_threads', $my_threads);
$this->tpl->set('my_threads_count', $this->model->threadFactory->count($filter));

$orderby = array('priority DESC', 'plan_date');
$filter = array('state' => 'opened', 'assignee' => $this->model->user_nick);
$my_tasks = $this->model->taskFactory->get_all($filter, $orderby);
$this->tpl->set('my_tasks', $my_tasks);
$this->tpl->set('my_tasks_count', $this->model->taskFactory->count($filter));

$orderby = array('sort', 'priority DESC', 'create_date DESC');
$filter = array('state' => 'opened', 'original_poster' => $this->model->user_nick);
$reported_threads = $this->model->threadFactory->get_all($filter, $orderby);
$this->tpl->set('reported_threads', $reported_threads);
$this->tpl->set('reported_threads_count', $this->model->threadFactory->count($filter));

$orderby = array('priority DESC', 'plan_date');
$filter = array('state' => 'opened', 'original_poster' => $this->model->user_nick);
$reported_tasks = $this->model->taskFactory->get_all($filter, $orderby);
$this->tpl->set('reported_tasks', $reported_tasks);
$this->tpl->set('reported_tasks_count', $this->model->taskFactory->count($filter));

