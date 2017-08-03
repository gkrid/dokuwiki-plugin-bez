<?php

$types = $this->model->tasktypes->get_all(array('refs'));

if ($this->param('id') === '') {	
    $tasktype = $this->model->tasktypes->create_object();
} else {
    $tasktype = $this->model->tasktypes->get_one($this->param('id'));
}

$this->tpl->set('types', $types);
$this->tpl->set('tasktype', $tasktype);

if ($this->param('action') === 'edit') {
    
    $this->tpl->set_values($tasktype->get_assoc());
    
} else if ($this->param('action') === 'remove') {
    
    $this->model->tasktypes->delete($tasktype);
    
    header('Location: ?id=bez:task_types');
    
} elseif (count($_POST) > 0) {
    $tasktype->set_data($_POST);
    $this->model->tasktypes->save($tasktype);
    
    header('Location: ?id=bez:task_types');
}

